<?php

class RandomsController extends \BaseController {

	/**
	 * Display a listing of randoms
	 *
	 * @return Response
	 */
	public function index()
	{
		$randoms = Random::all();

		return View::make('randoms.index', compact('randoms'));
	}

	/**
	 * Show the form for creating a new random
	 *
	 * @return Response
	 */
	public function create($challenge_id)
	{
		$challenge = Challenge::with('randoms')->findOrFail($challenge_id);
		$order = $challenge->randoms->max('display_order') + 1;

		return View::make('randoms.create')
				   ->with(compact('challenge_id', 'order'));
	}

	/**
	 * Store a newly created random in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::except('_token'), Random::$rules);

		if ($validator->fails())
		{
			return Redirect::route('randoms.create', Input::get('challenge_id'))->withErrors($validator)->withInput();
		}

		Random::create($data);

		return 'true';
	}

	/**
	 * Display the specified random.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$random = Random::findOrFail($id);

		return View::make('randoms.show', compact('random'));
	}

	/**
	 * Show the form for editing the specified random.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$random = Random::find($id);

		return View::make('randoms.edit', compact('random'));
	}

	/**
	 * Update the specified random in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$random = Random::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Random::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$random->update($data);

		return 'true';
	}

	/**
	 * Remove the specified random from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Random::destroy($id);

		return Redirect::back();
	}

}
