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

	public function mathteamscore($team_id, $show_judges = false) {
		Breadcrumbs::addCrumb('Math Programming Team Score', 'mathteamscore');
		$team = MathTeam::with('school', 'division', 'division.competition')->find($team_id);
		$division_id = $team->division->id;

		if(Roles::isJudge()) {
			$challenges = MathChallenge::where('division_id', $division_id)
						->with(array('scores_with_trash' => function($q) use ($team_id)
							{
								$q->where('team_id', $team_id);
							}, 'scores_with_trash.judge'))->get();
		} else {
			$challenges = MathChallenge::where('division_id', $division_id)
						->with(array('scores' => function($q) use ($team_id)
							{
								$q->where('team_id', $team_id);
							}, 'scores.judge'))->get();
		}

		//$div_challenges = MathDivision::with('challenges')->find($division_id)->challenges;

		$grand_total = 0;
		foreach($challenges as $challenge) {
			if(Roles::isJudge()) {
				$challenge->scores_with_trash->sort(function($a, $b) { return $a->run - $b->run; });
				$challenge->total = $challenge->scores_with_trash->filter(function($sr) { return !$sr->trashed(); } )->max('score');
			} else {
				$challenge->scores->sort(function($a, $b) { return $a->run - $b->run; });
				$challenge->total = $challenge->scores->max('score');
			}
			$grand_total += $challenge->total;
		}

		View::share('title', $team->longname() . ' Scores');
		return View::make('display.mathteamscore', compact('team','challenges', 'grand_total', 'show_judges'));
	}


	public function delete_score($team_id, $score_run_id)
	{
		$score_run = MathRun::find($score_run_id);
		if(Roles::isAdmin() OR $score_run->judge_id == Auth::user()->ID) {
			$score_run->delete();
			return Redirect::route('display.mathteamscore', [ $team_id ])->with('message', 'Score Deleted');
		}
		return Redirect::route('display.mathteamscore', [ $team_id ])->with('message', 'You do not have permission to delete this score.');
	}

	public function restore_score($team_id, $score_run_id)
	{
		$score_run = MathRun::withTrashed()->find($score_run_id);

		// Allow Admins or the judge who deleted the scores to restore them
		if(Roles::isAdmin() or $score_run->judge_id == Auth::user()->ID ) {
			$score_run->restore();
			return Redirect::route('display.mathteamscore', [ $team_id ])->with('message', 'Score Restored');
		}
		return Redirect::route('display.mathteamscore', [ $team_id ])->with('message', 'You do not have permission to restore this score.');
	}
}
