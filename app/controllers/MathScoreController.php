<?php

define('SCORE_COLUMNS', 6);

class MathScoreController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($competition_id = 0, $division_id = 0, $team_id = 0)
	{
		if($competition_id == 0) {
			$competitions = MathCompetition::all();
			return View::make('math_score.competition_list', compact('competitions'));
		}

		if($division_id == 0) {
			$divisions = MathDivision::where('competition_id', $competition_id)->get();
			return View::make('math_score.division_list', compact('divisions', 'competition_id'));
		}

		if($team_id == 0) {
			$teams = MathTeam::where('division_id', $division_id)->get();
			return View::make('math_score.team_list', compact('teams', 'division_id', 'competition_id'));
		}

		$challenges = MathDivision::with('challenges')->find($division_id)->challenges;
		$team = MathTeam::find($team_id);
		$judge = Judge::find(Auth::user()->ID);

		if(empty($team))
		{
			$teams = Team::where('division_id', $division_id)->get();
			return View::make('math_score.team_list', compact('teams', 'division_id', 'competition_id'));
		}

        return View::make('math_score.index')
        			->with(compact('challenges', 'team_id', 'team', 'judge', 'competition_id', 'division_id'));
	}

	public function doscore($team_id, $challenge_id)
	{
		$challenge = MathChallenge::with('division')->find($challenge_id);
		$team = MathTeam::with('school','division', 'division.competition')->find($team_id);
		$judge = Judge::find(Auth::user()->ID);

		if($challenge->division->id != $team->division_id) {
			return "Could not find that challenge for that team";
		}

		$run_number = MathRun::where('team_id',  $team_id)->where('challenge_id', $challenge_id)->max('run') + 1;

		return View::make('math_score.doscore')
					->with(compact('challenge', 'team', 'run_number', 'judge'))
					->with('competition_id', $team->division->competition->id)
					->with('division_id', $team->division->id);

	}


	public function save($team_id, $challenge_id)
	{
		$team = MathTeam::find($team_id);
		if(Input::has('cancel')) {
			return Redirect::route('math_score.score_team', ['team_id' => $team_id, 'competition_id' => $team->division->competition->id, 'divison_id' => $team->division_id])
				->with('message', 'Did not Score');
		}
		$challenge = MathChallenge::with('division')->find($challenge_id);

		if($challenge->division->id != $team->division_id) {
			return Redirect::route('math_score.score_team', ['team_id' => $team_id, 'competition_id' => $team->division->competition->id, 'divison_id' => $team->division_id])
					->with('message', "Cannot find that Division/Team Combination");
		}

		$run_number = MathRun::where('team_id',  $team_id)->where('challenge_id', $challenge_id)->max('run') + 1;

		$date = Carbon\Carbon::now('UTC')->setTimeZone("PDT");

		$newRun = [ 'run' => $run_number,
					'score' => Input::get('score', 0),
					'team_id' => $team_id,
					'judge_id' => Auth::user()->ID,
					'challenge_id' => $challenge_id,
					'division_id' => $team->division_id,
					'run_time' => $date->format('H:i:s') ];

		MathRun::create($newRun);

		return Redirect::route('math_score.score_team', ['team_id' => $team_id, 'competition_id' => $team->division->competition->id, 'divison_id' => $team->division_id]);
	}
}
