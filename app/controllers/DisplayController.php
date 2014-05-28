<?php

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

		$challenges = Challenge::with(array('scores' => function($q) use ($team_id)
						{
							$q->where('team_id', $team_id);
						}, 'scores.judge'))->get();

		//dd(DB::getQueryLog());

		$div_challenges = Division::with('challenges')->find($division_id)->challenges;

		$grand_total = 0;
		foreach($div_challenges as $div_challenge)
		{
			$challenge_number = $div_challenge->pivot->display_order;
			$challenge_list[$challenge_number]['name'] = $div_challenge->display_name;
			$challenge_list[$challenge_number]['points'] = $div_challenge->points;
			if($challenges->find($div_challenge->id)->scores->count() > 0)
			{
				$score_runs = $challenges->find($div_challenge->id)->scores;
				//$score_runs->load('judge');
				$challenge_list[$challenge_number]['score_count'] = $score_runs->count();
				$challenge_list[$challenge_number]['score_max'] = $score_runs->max('total');
				$grand_total += $challenge_list[$challenge_number]['score_max'];
				foreach($score_runs as $score_run)
				{
					$challenge_list[$challenge_number]['runs'][$score_run->run_number]['run_time'] = $score_run->run_time;
					$challenge_list[$challenge_number]['runs'][$score_run->run_number]['total'] = $score_run->total;
					$challenge_list[$challenge_number]['runs'][$score_run->run_number]['judge'] = $score_run->judge->display_name;
					$challenge_list[$challenge_number]['runs'][$score_run->run_number]['id'] = $score_run->id;
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

	public function compscore($competition_id, $do_not_freeze = null)
	{
		Breadcrumbs::addCrumb('Competition Score', 'compscore');

		$comp = Competition::with('divisions', 'divisions.teams', 'divisions.challenges')->find($competition_id);
		$divisions = $comp->divisions;

		// Event Timing
		$start_time = Carbon\Carbon::now()->setTimezone('America/Los_Angeles')->toTimeString();
		$this_event = Schedule::where('start', '<', $start_time)->orderBy('start', 'DESC')->first();
		$next_event = Schedule::where('start', '>', $start_time)->orderBy('start')->first();

		//dd(DB::getQueryLog());

		// Frozen Calculation
		$freeze_time = new Carbon\Carbon($comp->freeze_time);
		if($comp->frozen AND $start_time->gt($freeze_time) AND !isset($do_not_freeze)) {
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
					->select('team_id', 'challenge_id', DB::raw('max(total) as chal_score'))
					->groupBy('team_id', 'challenge_id')
					->orderBy('team_id', 'challenge_id')
					->where('division_id', $division->id)
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
					$score_list[$division->id][$score->team_id] = 0;
				}
				$score_list[$division->id][$score->team_id] += $score->chal_score;
			}


			// Find all of the teams with no scores yet and add them to the end of the list
			$team_list = $division->teams->lists('id');
			$missing_list = array_diff($team_list, array_keys($score_list[$division->id]));
			$score_list[$division->id] = $score_list[$division->id] + array_fill_keys($missing_list, 0);

			// Descending sort by score
			arsort($score_list[$division->id]);
		}

		// Setup column widths depending on number of divisions
		switch($divisions->count())
		{
			case 1:
				$col_class = "";
				break;
			case 2:
				$col_class = "col-md-6 col-lg-6";
				break;
			case 3:
				$col_class = "col-md-4 col-lg-4";
				break;
			default:
				$col_class = "col-md-2 col-lg-2";
		}



		View::share('title', $comp->name . ' Scores');
		return View::make('display.compscore', compact('comp', 'divisions', 'score_list', 'col_class', 'this_event', 'next_event', 'frozen', 'start_time'));
	}

	public function delete_score($team_id, $score_run_id)
	{
		if(Roles::isAdmin()) {
			$score_run = Score_run::find($score_run_id);
			$score_run->delete();
		}
		return Redirect::route('display.teamscore', [ $team_id ]);
	}
}
