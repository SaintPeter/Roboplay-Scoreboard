<?php

class DisplayController extends BaseController {

	/**
	 * Show the detailed score for a single team for all challenges
	 *
	 * @return Response
	 */
	public function teamscore($team_id)
	{
		$team = Team::with('division')->find($team_id);
		$division_id = $team->division->id;

		$challenges = Challenge::with(array('scores' => function($q) use ($team_id)
						{
							$q->where('team_id', $team_id);
						}))->get();

		$div_challenges = Division::with('challenges')->find($division_id)->challenges;

		$grand_total = 0;
		foreach($div_challenges as $div_challenge)
		{
			$challenge_number = $div_challenge->pivot->display_order;
			$challenge_list[$challenge_number]['name'] = $div_challenge->display_name;
			if($challenges->find($div_challenge->id)->scores->count() > 0)
			{
				$score_runs = $challenges->find($div_challenge->id)->scores;
				$challenge_list[$challenge_number]['score_count'] = $score_runs->count();
				$challenge_list[$challenge_number]['score_max'] = $score_runs->max('total');
				$grand_total += $challenge_list[$challenge_number]['score_max'];
				foreach($score_runs as $score_run)
				{
					$challenge_list[$challenge_number]['runs'][$score_run->run_number]['run_time'] = $score_run->run_time;
					$challenge_list[$challenge_number]['runs'][$score_run->run_number]['total'] = $score_run->total;
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

		return View::make('display.teamscore', compact('team','challenge_list', 'grand_total'));
	}

	public function compscore($competition_id)
	{
		$comp = Competition::with('divisions', 'divisions.teams')->find($competition_id);
		$divisions = $comp->divisions;

		$score_list = array();
		foreach($divisions as $division)
		{
			$score_list[$division->id] = array();

			// Calculate the max score for each team and challenge
			$scores = DB::table('score_runs')
					->select('team_id', 'challenge_id', DB::raw('max(total) as chal_score'))
					->groupBy('team_id', 'challenge_id')
					->orderBy('team_id', 'challenge_id')
					->where('division_id', $division->id)
					->get();

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

		return View::make('display.compscore', compact('comp', 'divisions', 'score_list', 'col_class'));
	}

}
