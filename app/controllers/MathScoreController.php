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

	// Take a list of input values and turn them into mathscores
	function calculate_mathscores($value_list, $challenge_id)
	{
		$challenge = Challenge::with('mathscore_elements')->find($challenge_id);

		$mathscores = array_fill(1, SCORE_COLUMNS, '-');
		$total = 0;
		foreach($challenge->mathscore_elements as $se) {
			if($se->type == 'mathscore_slider') {
				// With a mathscore slider the multiplier is always 1
				$mathscores[$se->element_number] = $se->base_value + intval($value_list[$se->id]['value']);
			} else {
				// Everything else uses the multiplier
				$mathscores[$se->element_number] = $se->base_value + (intval($value_list[$se->id]['value']) * $se->multiplier);
			}
			$total += $mathscores[$se->element_number];
		}
		$total = max($total, 0);

		return array($mathscores, $total);
	}

	// Return an array with aborts filled in
	public function abort_mathscores($challenge_id) {
		$challenge = Challenge::with('mathscore_elements')->find($challenge_id);

		$mathscores = array_fill(1, SCORE_COLUMNS, '-');
		foreach($challenge->mathscore_elements as $se) {
			$mathscores[$se->element_number] = 'A';
		}
	 return $mathscores;
	}


	public function save($team_id, $challenge_id)
	{
		$team = Team::find($team_id);
		if(Input::has('cancel')) {
			return Redirect::route('math_score.mathscore_team', ['team_id' => $team_id, 'competition_id' => $team->division->competition->id, 'divison_id' => $team->division_id])
				->with('message', 'Did not Score');
		}
		$challenge = Challenge::with('mathscore_elements','divisions')->find($challenge_id);

		if(!$challenge->divisions->contains($team->division_id)) {
			return View::make('math_score.domathscore')
					->with(compact('challenge', 'team', 'run_number'))
					->with('mathscore_elements', $challenge->mathscore_elements)
					->with('message', "Cannot find that Division/Team Combination")
					->withInput();
		}

		if(Input::has('abort')) {
			$total = 0;
			$mathscores = $this->abort_mathscores($challenge_id);
		} else {
			$value_list = Input::get('mathscores', array());
			list($mathscores, $total) = $this->calculate_mathscores($value_list, $challenge_id);
		}

		$run_number = Score_run::where('team_id',  $team_id)->where('challenge_id', $challenge_id)->max('run_number') + 1;

		$date = Carbon\Carbon::now('UTC')->setTimeZone("PDT");

		$newRun = array('run_number' => $run_number,
						'mathscores' => $mathscores,
						'total' => $total,
						'team_id' => $team_id,
						'judge_id' => Auth::user()->ID,
						'challenge_id' => $challenge_id,
						'division_id' => $team->division_id,
						'run_time' => $date->format('H:i:s'));

		Score_run::create($newRun);

		return Redirect::route('math_score.mathscore_team', ['team_id' => $team_id, 'competition_id' => $team->division->competition->id, 'divison_id' => $team->division_id]);
	}
}
