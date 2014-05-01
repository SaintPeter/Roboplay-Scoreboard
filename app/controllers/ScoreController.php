<?php

class ScoreController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($competition_id = 0, $division_id = 0, $team_id = 0)
	{
		if($competition_id == 0) {
			$competitions = Competition::all();
			return View::make('score.competition_list', compact('competitions'));
		}

		if($division_id == 0) {
			$divisions = Division::where('competition_id', $competition_id)->get();
			return View::make('score.division_list', compact('divisions', 'competition_id'));
		}

		if($team_id == 0) {
			$teams = Team::with('school')->where('division_id', $division_id)->get();
			return View::make('score.team_list', compact('teams', 'division_id', 'competition_id'));
		}

		$challenges = Division::with('challenges', 'challenges.score_elements')
								->find($division_id)->challenges;
		$team = Team::with('school')->find($team_id);
		$judge = Judge::find(Auth::user()->ID);

		if(empty($team))
		{
			$teams = Team::with('school')->where('division_id', $division_id)->get();
			return View::make('score.team_list', compact('teams', 'division_id', 'competition_id'));
		}

        return View::make('score.index')
        			->with(compact('challenges', 'team_id', 'team', 'judge', 'competition_id', 'division_id'));
	}

	public function doscore($team_id, $challenge_id)
	{
		$challenge = Challenge::with('score_elements','divisions')->find($challenge_id);
		$team = Team::with('school')->with('division', 'division.competition')->find($team_id);
		$judge = Judge::find(Auth::user()->ID);

		if(!$challenge->divisions->contains($team->division_id)) {
			return "Could not find that challenge for that team";
		}

		$run_number = Score_run::where('team_id',  $team_id)->where('challenge_id', $challenge_id)->count() + 1;

		return View::make('score.doscore')
					->with(compact('challenge', 'team', 'run_number', 'judge'))
					->with('competition_id', $team->division->competition->id)
					->with('division_id', $team->division->id)
					->with('score_elements', $challenge->score_elements);

	}

	public function preview($team_id, $challenge_id)
	{
		$challenge = Challenge::with('score_elements','divisions')->find($challenge_id);
		$team = Team::with('school')->find($team_id);

		if(!$challenge->divisions->contains($team->division_id)) {
			return View::make('score.doscore')
					->with(compact('challenge', 'team', 'run_number'))
					->with('score_elements', $challenge->score_elements)
					->with('message', "Cannot find that Division/Team Combination")
					->withInput();
		}

		$value_list = Input::get('scores', array());
		list($scores, $total) = $this->calculate_scores($value_list, $challenge_id);

		$run_number = Score_run::where('team_id',  $team_id)->where('challenge_id', $challenge_id)->count() + 1;

		return View::make('score.preview')
					->with(compact('challenge', 'team', 'run_number', 'scores', 'total'))
					->with('score_elements', $challenge->score_elements);
	}

	function calculate_scores($value_list, $challenge_id)
	{
		$challenge = Challenge::with('score_elements')->find($challenge_id);

		$scores = array_fill(1, 10, '-');
		$total = 0;
		foreach($challenge->score_elements as $se) {
			$scores[$se->element_number] = $se->base_value + (intval($value_list[$se->id]['value']) * $se->multiplier);
			$total += $scores[$se->element_number];
		}
		$total = max($total, 0);

		return array($scores, $total);
	}


	public function save($team_id, $challenge_id)
	{
		if(Input::has('cancel')) {
			return Redirect::route('score.index')
				->with('message', 'Did not Score');
		}
		$challenge = Challenge::with('score_elements','divisions')->find($challenge_id);
		$team = Team::with('school')->find($team_id);

		if(!$challenge->divisions->contains($team->division_id)) {
			return View::make('score.doscore')
					->with(compact('challenge', 'team', 'run_number'))
					->with('score_elements', $challenge->score_elements)
					->with('message', "Cannot find that Division/Team Combination")
					->withInput();
		}

		$value_list = Input::get('scores', array());
		list($scores, $total) = $this->calculate_scores($value_list, $challenge_id);

		$run_number = Score_run::where('team_id',  $team_id)->where('challenge_id', $challenge_id)->count() + 1;

		$newRun = array('run_number' => $run_number,
						'scores' => $scores,
						'total' => $total,
						'team_id' => $team_id,
						'judge_id' => Auth::user()->ID,
						'challenge_id' => $challenge_id,
						'division_id' => $team->division_id);

		Score_run::create($newRun);

		return Redirect::route('score.index');
	}

	public function competition($competition_id)
	{
		if(Competition::find($competition_id)->count() > 0) {
			Session::put('currentCompetition', $competition_id);
			Session::forget('currentDivision');
			Session::forget('currentTeam');
			return Redirect::route('score.index');
		} else {
			return Redirect::route('divisions.index')
							->with('message', 'Unable to find specified Competition');
		}
	}

	public function division($division_id)
	{
		if(Division::find($division_id)->count() > 0) {
			Session::put('currentDivision', $division_id);
			Session::forget('currentTeam');
			return Redirect::route('score.index');
		} else {
			return Redirect::route('divisions.index')
							->with('message', 'Unable to find specified Division');
		}
	}

	public function team($team_id)
	{
		if(Team::with('school')->find($team_id)->count() > 0) {
			Session::put('currentTeam', $team_id);
			return Redirect::route('score.index');
		} else {
			return Redirect::route('teams.index')
							->with('message', 'Unable to find specified Team');
		}
	}
}
