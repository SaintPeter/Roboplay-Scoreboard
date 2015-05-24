<?php

class MathChallengesController extends \BaseController {

	/**
	 * Display a listing of math_challenges
	 *
	 * @return Response
	 */
	public function index()
	{
		$math_challenges = MathChallenge::all();

		return View::make('math_challenges.index', compact('math_challenges'));
	}

	/**
	 * Show the form for creating a new math_challenge
	 *
	 * @return Response
	 */
	public function create($division_id = null)
	{
		$division_list = MathDivision::longname_array();
		$order = MathChallenge::where('division_id', $division_id)->max('order') + 1;

		return View::make('math_challenges.partial.create',compact('order','division_list','division_id'));
	}

	/**
	 * Store a newly created math_challenge in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$data = Input::all();
		$data['year'] = Carbon\Carbon::now()->year;
		$validator = Validator::make($data, MathChallenge::$rules);

		if ($validator->fails())
		{
			return Redirect::route('math_challenges.create', Input::get('division_id'))->withErrors($validator)->withInput();
		}

		MathChallenge::create($data);

		return 'true';
	}

	/**
	 * Display the specified math_challenge.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$math_challenge = MathChallenge::findOrFail($id);

		return View::make('math_challenges.show', compact('math_challenge'));
	}

	/**
	 * Show the form for editing the specified math_challenge.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$math_challenge = MathChallenge::find($id);

		return View::make('math_challenges.partial.edit', compact('math_challenge'));
	}

	/**
	 * Update the specified math_challenge in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$math_challenge = MathChallenge::findOrFail($id);

		$validator = Validator::make($data = Input::all(), array_except(MathChallenge::$rules, [ 'year', 'division_id'] ));

		if ($validator->fails())
		{
			return Redirect::route('math_challenges.edit', $id)->withErrors($validator)->withInput();
		}

		$math_challenge->update($data);

		// Reorder challenges attachd to parent division_id
		$order = 1;
		$division = MathDivision::with('challenges')->find($math_challenge->division->id);
		DB::transaction(function() use ($division) {
			$order = 1;
			foreach($division->challenges as $challenge) {
				DB::table($challenge->getTable())
					->where('id', $challenge->id)
					->update([ 'order' => $order ]);
				$order++;
			}
		});

		return 'true';
	}

	/**
	 * Remove the specified math_challenge from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		MathChallenge::destroy($id);

		return Redirect::route('math_challenges.index');
	}

}
