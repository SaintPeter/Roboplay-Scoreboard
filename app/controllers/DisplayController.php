<?php

use \Carbon\Carbon;

class DisplayController extends BaseController {

	/**
	 * Show the detailed score for a single team for all challenges
	 *
	 * @return Response
	 */
	public function teamscore($team_id, $show_judges = null)
	{
		Breadcrumbs::addCrumb('Team Score', 'teamscore');
		$team = Team::with('division', 'division.competition')->find($team_id);
		$division_id = $team->division->id;

		if(Roles::isJudge()) {
			$challenges = Challenge::with(array('scores_with_trash' => function($q) use ($team_id)
							{
								$q->where('team_id', $team_id);
							}, 'scores.judge'))->get();
		} else {
			$challenges = Challenge::with(array('scores' => function($q) use ($team_id)
							{
								$q->where('team_id', $team_id);
							}, 'scores.judge'))->get();

		}

		//dd(DB::getQueryLog());

		$div_challenges = Division::with('challenges')->find($division_id)->challenges;

		$grand_total = 0;
		foreach($div_challenges as $div_challenge)
		{
			$challenge_number = $div_challenge->pivot->display_order;
			$challenge_list[$challenge_number]['name'] = $div_challenge->display_name;
			$challenge_list[$challenge_number]['points'] = $div_challenge->points;

			if(Roles::isJudge()) {
				$count = $challenges->find($div_challenge->id)->scores_with_trash->count();
			} else {
				$count = $challenges->find($div_challenge->id)->scores->count();
			}

			if($count > 0)
			{
				// Judges may see deleted scores
				if(Roles::isJudge()) {
					$score_runs = $challenges->find($div_challenge->id)->scores_with_trash;
				} else {
					$score_runs = $challenges->find($div_challenge->id)->scores;
				}
				//$score_runs->load('judge');
				$challenge_list[$challenge_number]['score_count'] = $score_runs->count();
				$challenge_list[$challenge_number]['score_max'] = $score_runs->filter(function($sr) { return !$sr->trashed(); } )->max('total');
				$grand_total += $challenge_list[$challenge_number]['score_max'];
				//dd($score_runs);
				foreach($score_runs as $score_run)
				{
					$challenge_list[$challenge_number]['runs'][$score_run->run_number]['run_time'] = $score_run->run_time;
					$challenge_list[$challenge_number]['runs'][$score_run->run_number]['total'] = $score_run->total;
					$challenge_list[$challenge_number]['runs'][$score_run->run_number]['judge'] = $score_run->judge->display_name;
					$challenge_list[$challenge_number]['runs'][$score_run->run_number]['is_judge'] = Auth::check() ? ($score_run->judge->id == Auth::user()->ID) : 0;
					$challenge_list[$challenge_number]['runs'][$score_run->run_number]['id'] = $score_run->id;
					$challenge_list[$challenge_number]['runs'][$score_run->run_number]['deleted'] = $score_run->trashed();
					$score_index = 0;
					foreach($score_run->scores as $score_element)
					{
						$challenge_list[$challenge_number]['runs'][$score_run->run_number]['scores'][$score_index] = $score_element;
						$score_index++;
					}
				}
				$challenge_list[$challenge_number]['has_scores'] = true;
			} else {
				$challenge_list[$challenge_number]['has_scores'] = false;
			}
		}

		//dd(DB::getQueryLog());
		View::share('title', $team->longname() . ' Scores');
		return View::make('display.teamscore', compact('team','challenge_list', 'grand_total', 'show_judges'));
	}

	public function compscore($competition_id, $csv = null)
	{
		Breadcrumbs::addCrumb('Competition Score', 'compscore');

		$comp = Competition::with('divisions', 'divisions.teams', 'divisions.challenges')->find($competition_id);
		$divisions = $comp->divisions;

		// Event Timing
		$start_time = Carbon::now()->setTimezone('America/Los_Angeles')->toTimeString();
		$this_event = Schedule::where('start', '<', $start_time)->orderBy('start', 'DESC')->first();
		$next_event = Schedule::where('start', '>', $start_time)->orderBy('start')->first();

		//dd(DB::getQueryLog());

		// Frozen Calculation
		$freeze_time = new Carbon($comp->freeze_time);
		if($comp->frozen AND isset($start_time->freeze_time)) {
			$frozen = true;
		} else {
			$frozen = false;
		}

		// Get score list and calculate totals
		$score_list = array();
		foreach($divisions as $division)
		{
			$score_list[$division->id] = array();
			$challenge_list = $division->challenges->lists('id');

			// Calculate the max score for each team and challenge
			$scores = DB::table('score_runs')
					->select('team_id', 'challenge_id', DB::raw('max(total) as chal_score'), DB::raw('count(total) as chal_runs'))
					->groupBy('team_id', 'challenge_id')
					->orderBy('team_id', 'challenge_id')
					->where('division_id', $division->id)
					->whereNull('deleted_at')
					->whereIn('challenge_id', $challenge_list);  // Limit to currently attached challenges

			// If we're frozen, limit scores we count by the freeze time
			if($frozen) {
				$scores = $scores->where('run_time', '<=', $freeze_time->toTimeString())->get();
			} else {
				$scores = $scores->get();
			}

			// Sum up all of the scores by team
			foreach($scores as $score)
			{
				// Initalize the storage location for each team
				if(!array_key_exists($score->team_id, $score_list[$division->id])) {
					$score_list[$division->id][$score->team_id]['total'] = 0;
					$score_list[$division->id][$score->team_id]['runs'] = 0;
				}
				$score_list[$division->id][$score->team_id]['total'] += $score->chal_score;
				$score_list[$division->id][$score->team_id]['runs'] += $score->chal_runs;
			}


			// Find all of the teams with no scores yet and add them to the end of the list
			$team_list = $division->teams->lists('id');
			$missing_list = array_diff($team_list, array_keys($score_list[$division->id]));
			$score_list[$division->id] = $score_list[$division->id] + array_fill_keys($missing_list, [ 'total' => 0, 'runs' => 0 ] );

			// Descending sort by score
			//arsort($score_list[$division->id]);

			// Sort descening by Score, runs
			//    minus is a standin for the <=> operator
			//    a - b roughly equals a <=> b
			uasort($score_list[$division->id], function($a, $b) {
				// Sort by score first:
				if($a['total'] == $b['total']) {
					return $a['runs'] - $b['runs'];
				} else {
					return $b['total'] - $a['total'];
				}
			});

			// Populate Places
			$place = 1;
			foreach($score_list[$division->id] as $team_id => $scores) {
				if($score_list[$division->id][$team_id]['runs'] > 0) {
					$score_list[$division->id][$team_id]['place'] = $place++;
				} else {
					// Teams with no runs have no place
					$score_list[$division->id][$team_id]['place'] = '-';
				}
			}
		}

		// CSV Output
		if($csv) {
			$output = "Division,Place,Team,School,Score,Runs\n";

			foreach($divisions as $division) {
				foreach($score_list[$division->id] as $team_id => $score) {
					$output .= '"' . join('","', [
						$division->name,
						$score['place'],
						$division->teams->find($team_id)->name,
						$division->teams->find($team_id)->school->name,
						$score['total'],
						$score['runs']
					]) . "\"\n";
				}
			}

			$filename = str_replace(' ', '_', $comp->name) . '.csv';
			$headers = [
				'Content-Type' => 'text/csv',
				'Content-Disposition' => 'attachment; filename="' . $filename . '"'
			];

			return Response::make(rtrim($output, "\n"), 200, $headers);
		}


		$now = Carbon::now()->setTimezone('America/Los_Angeles');
		$event = new Carbon($comp->event_date);
		$display_timer = $now->isSameDay($event) || Input::get('display_timer', false);

		// Pull settings from the session variable
		$session_variable = "compsettings_$competition_id";
		$settings['columns'] = Session::get($session_variable . '_columns', 1);
		$settings['rows'] = Session::get($session_variable . '_rows', 15);
		$settings['delay'] = Session::get($session_variable . '_delay', 3000);
		$settings['font-size'] = Session::get($session_variable . '_font-size', 'x-large');

		View::share('title', $comp->name . ' Scores');
		return View::make('display.compscore', compact('comp', 'divisions', 'score_list',
													   'col_class', 'this_event', 'next_event',
													   'frozen', 'start_time', 'display_timer',
													   'settings'));
	}

	public function compyearscore($compyear_id, $csv = null)
	{
		Breadcrumbs::addCrumb('Statewide Score', 'compyearscore');

		$compyear = CompYear::with('divisions', 'divisions.teams', 'divisions.challenges')->find($compyear_id);
		$divisions = $compyear->divisions;

		// Get score list and calculate totals
		$score_list = [];
		$teams = NULL;
		foreach($divisions as $division)
		{
			if($teams) {
				$teams = $teams->merge($division->teams);
			} else {
				$teams = $division->teams;
			}

			//echo '<pre>' . print_r($teams->lists('name'),true) . '</pre>';

			if(!array_key_exists($division->level, $score_list)) {
				$score_list[$division->level] = [];
			}
			$challenge_list = $division->challenges->lists('id');

			// Calculate the max score for each team and challenge
			$scores = DB::table('score_runs')
					->select('team_id', 'challenge_id', DB::raw('max(total) as chal_score'), DB::raw('count(total) as chal_runs'))
					->groupBy('team_id', 'challenge_id')
					->orderBy('team_id', 'challenge_id')
					->where('division_id', $division->id)
					->whereNull('deleted_at')
					->whereIn('challenge_id', $challenge_list)  // Limit to currently attached challenges
					->get();

//dd($scores);

			// Sum up all of the scores by team
			foreach($scores as $score)
			{
				// Initalize the storage location for each team
				if(!array_key_exists($score->team_id, $score_list[$division->level])) {
					$score_list[$division->level][$score->team_id]['total'] = 0;
					$score_list[$division->level][$score->team_id]['runs'] = 0;
				}
				$score_list[$division->level][$score->team_id]['total'] += $score->chal_score;
				$score_list[$division->level][$score->team_id]['runs'] += $score->chal_runs;
			}


			// Find all of the teams with no scores yet and add them to the end of the list
			$team_list = $division->teams->lists('id');
			$missing_list = array_diff($team_list, array_keys($score_list[$division->level]));
			$score_list[$division->level] = $score_list[$division->level] + array_fill_keys($missing_list, [ 'total' => 0, 'runs' => 0 ] );

			//echo '<pre>' . print_r($score_list,true) . '</pre>';

			// Sort descening by Score, runs
			//    minus is a standin for the <=> operator
			//    a - b roughly equals a <=> b
			uasort($score_list[$division->level], function($a, $b) {
				// Sort by score first:
				if($a['total'] == $b['total']) {
					return $a['runs'] - $b['runs'];
				} else {
					return $b['total'] - $a['total'];
				}
			});

			// Populate Places
			$place = 1;
			foreach($score_list[$division->level] as $team_id => $scores) {
				if($score_list[$division->level][$team_id]['runs'] > 0) {
					$score_list[$division->level][$team_id]['place'] = $place++;
				} else {
					// Teams with no runs have no place
					$score_list[$division->level][$team_id]['place'] = '-';
				}
			}
		}

		// CSV Output
		if($csv) {
			$output = "Division,Place,Team,School,Score,Runs\n";

			foreach($score_list as $level => $scores) {
				foreach($scores as $team_id => $score) {
					$output .= '"' . join('","', [
						'Division ' . $level,
						$score['place'],
						$teams->find($team_id)->name,
						$teams->find($team_id)->school->name,
						$score['total'],
						$score['runs']
					]) . "\"\n";
				}
			}

			$filename = str_replace(' ', '_', 'RoboPlay Competition ' . $compyear->year) . '.csv';
			$headers = [
				'Content-Type' => 'text/csv',
				'Content-Disposition' => 'attachment; filename="' . $filename . '"'
			];

			return Response::make(rtrim($output, "\n"), 200, $headers);
		}

		$now = Carbon::now()->setTimezone('America/Los_Angeles');
		//$event = new Carbon($comp->event_date);
		$display_timer = true; //$now->isSameDay($event) || Input::get('display_timer', false);

		// Event Timing
		$start_time = Carbon::now()->setTimezone('America/Los_Angeles')->toTimeString();
		$this_event = Schedule::where('start', '<', $start_time)->orderBy('start', 'DESC')->first();
		$next_event = Schedule::where('start', '>', $start_time)->orderBy('start')->first();
		$frozen = false;

		// Pull settings from the session variable
		$session_variable = "compyearsettings_$compyear_id";
		$settings['columns'] = Session::get($session_variable . '_columns', 1);
		$settings['rows'] = Session::get($session_variable . '_rows', 15);
		$settings['delay'] = Session::get($session_variable . '_delay', 3000);
		$settings['font-size'] = Session::get($session_variable . '_font-size', 'x-large');

//dd($teams);
//exit;
		View::share('title', 'RoboPlay ' . $compyear->year . ' Scores');
		return View::make('display.compyearscore', compact('compyear', 'teams', 'score_list',
													   'col_class', 'this_event', 'next_event',
													   'frozen', 'start_time', 'display_timer',
													   'settings'));
	}

	public function delete_score($team_id, $score_run_id)
	{
		$score_run = Score_run::find($score_run_id);
		if(Roles::isAdmin() OR $score_run->judge_id == Auth::user()->ID) {
			$score_run->delete();
			return Redirect::route('display.teamscore', [ $team_id ])->with('message', 'Score Deleted');
		}
		return Redirect::route('display.teamscore', [ $team_id ])->with('message', 'You do not have permission to delete this score.');
	}

	public function restore_score($team_id, $score_run_id)
	{
		$score_run = Score_run::withTrashed()->find($score_run_id);

		// Allow Admins or the judge who deleted the scores to restore them
		if(Roles::isAdmin() or $score_run->judge_id == Auth::user()->ID ) {
			$score_run->restore();
			return Redirect::route('display.teamscore', [ $team_id ])->with('message', 'Score Restored');
		}
		return Redirect::route('display.teamscore', [ $team_id ])->with('message', 'You do not have permission to restore this score.');
	}

	public function challenge_students_csv() {
		$content = 'School,Team,"Student Name"' . "\n";

		$comps = Competition::with('divisions')->where('name', 'not like', DB::raw('"%test%"'))->get();
		$div_list = [];
		foreach($comps as $comp) {
			$div_list = array_merge($div_list, $comp->divisions->lists('id'));
		}
		$teams = Team::with('school')->whereIn('division_id', $div_list)->get();

		foreach($teams as $team) {
			foreach($team->student_list() as $student) {
				$content .= '"' . $team->school->name . '","' . $team->name	. '","' . $student . "\"\n";
			}
		}
		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="challenge_student.csv'
		));
	}

	public function video_students_csv() {
		$content = 'Division,School,Video,"Student Name"' . "\n";

		$comps = Vid_competition::with('divisions')->where('name', 'not like', DB::raw('"%test%"'))->get();
		$div_list = [];
		foreach($comps as $comp) {
			$div_list = array_merge($div_list, $comp->divisions->lists('id'));
		}
		$videos = Video::with('school', 'vid_division')->whereIn('vid_division_id', $div_list)->get();

		foreach($videos as $video) {
			foreach($video->student_list() as $student) {
				$content .= '"' . $video->vid_division->name . '","'. $video->school->name . '","' . $video->name	. '","' . $student . "\"\n";
			}
		}
		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="video_student.csv'
		));
	}

	public function video_list($competition_id)
	{
		$comp = Vid_competition::with('divisions')->find($competition_id);

		Breadcrumbs::addCrumb($comp->name . ' Videos', route('display.video_list', [ $competition_id ]));
		View::share('title', $comp->name . ' | Video List');

		//dd(DB::getQueryLog());
		$divs = [0];
		foreach($comp->divisions as $div) {
			$divs[] = $div->id;
		}

		$video_list = Video::with('school', 'vid_division')
		                   ->where('flag', FLAG_NORMAL)
		                   ->whereIn('vid_division_id', $divs)->get();

		$videos = [];
		foreach($video_list as $video) {
			$videos[$video->vid_division->name][$video->name] = $video;
		}

		return View::make('display.video_list', compact('videos', 'comp'));
	}

	public function show_video($competition_id, $video_id)
	{
		Breadcrumbs::addCrumb('Video List', route('display.video_list', [ $competition_id ]));
		Breadcrumbs::addCrumb('Video', '');
		View::share('title', 'Show Video');

		$video = Video::find($video_id);
		if(empty($video)) {
			// Invalid video
			return Redirect::route('display.show_videos', [ $competition_id ])
							->with('message', "Invalid video id '$video_id'.  Video no longer exists or another error occured.");
		}

		return View::make('display.show_video', compact('video'));
	}

	public function compsettings($competition_id) {
		$session_variable = "compsettings_$competition_id";
		Session::set($session_variable . '_columns', Input::get('columns', 1));
		Session::set($session_variable . '_rows', Input::get('rows', 15));
		Session::set($session_variable . '_delay', Input::get('delay', 3000));
		Session::set($session_variable . '_font-size', Input::get('font-size', 'x-large'));

		return Redirect::route('display.compscore', [ $competition_id ]);
	}

	public function compyearsettings($compyear_id) {
		$session_variable = "compyearsettings_$compyear_id";
		Session::set($session_variable . '_columns', Input::get('columns', 1));
		Session::set($session_variable . '_rows', Input::get('rows', 15));
		Session::set($session_variable . '_delay', Input::get('delay', 3000));
		Session::set($session_variable . '_font-size', Input::get('font-size', 'x-large'));

		return Redirect::route('display.compyearscore', [ $compyear_id ]);
	}

	public function export_year_scores($year) {
	    $compyear = CompYear::with('divisions', 'divisions.teams', 'divisions.challenges')->where('year', $year)->first();
		$divisions = $compyear->divisions;
		$division_list = $divisions->lists('id');

		$content = "Division,Challenge,Team,Run,Score,s1,s2,s3,s4,s5,s6,Used\n";

		foreach ($divisions as $division) {
		    $division_id = $division->id;

//            $challenges = Challenge::with( [ 'scores' => function($q) use ($division_id)
//							{
//								$q->where('division_id', $division_id);
//							} ] )->get();

            $division->challenges = $division->challenges->filter( function($val) use ($division_id)
							{
								return ($val->pivot->division_id === $division_id);
							});

			foreach ($division->challenges as $challenge) {
			    foreach ($challenge->scores as $score) {
    			    $line = [];
    			    $team = $division->teams->find($score->team_id);
    			    if(!$team) {
    			        continue;
    			    }

    			    $challenge_number = $challenge->pivot->display_order;
    			    $num = sprintf('%02d ', $challenge_number);

    			    $line = [
    			              $division->name,
    			              $num . $challenge->display_name,
    			              $team->name,
    			              $score->run_number,
    			              $score->total
    			            ];
                    $used = 0;
                    foreach ($score->scores as $points) {
                        $line[] = $points;
                        if($points != '-' and $points > 0) {
                            $used = $used + 1;
                        }
                    }
                    $line[] = $used;

    			    $content .= join($line, ',') . "\n";
			    }
			}
	    }

	    // return an string as a file to the user
		return Response::make($content, '200', [
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="raw_scores_' . $year . '.csv"'
		]);
	}
}
