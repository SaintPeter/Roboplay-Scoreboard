<?php

use \Carbon\Carbon;

class MathDisplayController extends BaseController {

	public function mathcompscore($competition_id)
	{
		Breadcrumbs::addCrumb('Competition Score', 'compscore');

		$comp = MathCompetition::with('divisions', 'divisions.teams', 'divisions.challenges')->find($competition_id);
		$divisions = $comp->divisions;

		// Get score list and calculate totals
		foreach($divisions as $division)
		{
			$score_list[$division->id] = [];

			// Get lookups
			$challenge_list = $division->challenges->lists('id');
			$challenge_count = $division->challenges->count();

			// Abort if there are no challenges
			if($challenge_count == 0) {
				return Redirect::back()->with('message', 'No Challenges Defined Yet');
			}

			// Initialize Score List
			foreach($division->teams as $team) {
				$score_list[$division->id][$team->id]['total'] = 0;
				$score_list[$division->id][$team->id]['runs'] = 0;
				$score_list[$division->id][$team->id]['scores'] = array_fill(1, $challenge_count, '-');
			}

			// Calculate the max score for each team and challenge
			$scores = DB::table('math_runs')
					->select('team_id', 'challenge_id', DB::raw('max(score) as chal_score'), DB::raw('count(score) as chal_runs'))
					->groupBy('team_id', 'challenge_id')
					->orderBy('team_id', 'challenge_id')
					->where('division_id', $division->id)
					->whereNull('deleted_at')
					->whereIn('challenge_id', $challenge_list)  // Limit to currently attached challenges
					->get();

			// Sum up all of the scores by team
			foreach($scores as $score)
			{
				$challenge = $division->challenges->find($score->challenge_id);
				// Collect the score and round up
				$this_score = min(ceil($score->chal_score * $challenge->multiplier), $challenge->points);
				$score_list[$division->id][$score->team_id]['total'] += $this_score;
				$score_list[$division->id][$score->team_id]['runs'] += $score->chal_runs;
				$score_list[$division->id][$score->team_id]['scores'][$challenge->order] = $this_score;
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

		View::share('title', $comp->name . ' Scores');
		return View::make('display.mathcompscore', compact('comp', 'divisions', 'score_list'));
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

		$video_list = Video::with('school', 'vid_division')->whereIn('vid_division_id', $divs)->get();

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
}
