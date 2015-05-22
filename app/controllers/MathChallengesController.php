<?php

class MathChallengesController extends \BaseController {

	/**
	 * Display a listing of mathchallenges
	 *
	 * @return Response
	 */
	public function index()
	{
		$mathchallenges = Mathchallenge::all();

		return View::make('mathchallenges.index', compact('mathchallenges'));
	}

	/**
	 * Show the form for creating a new mathchallenge
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('mathchallenges.create');
	}

	/**
	 * Store a newly created mathchallenge in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Mathchallenge::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Mathchallenge::create($data);

		return Redirect::route('mathchallenges.index');
	}

	/**
	 * Display the specified mathchallenge.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$mathchallenge = Mathchallenge::findOrFail($id);

		return View::make('mathchallenges.show', compact('mathchallenge'));
	}

	/**
	 * Show the form for editing the specified mathchallenge.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$mathchallenge = Mathchallenge::find($id);

		return View::make('mathchallenges.edit', compact('mathchallenge'));
	}

	/**
	 * Update the specified mathchallenge in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$mathchallenge = Mathchallenge::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Mathchallenge::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$mathchallenge->update($data);

		return Redirect::route('mathchallenges.index');
	}

	/**
	 * Remove the specified mathchallenge from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Mathchallenge::destroy($id);

		return Redirect::route('mathchallenges.index');
	}

}
