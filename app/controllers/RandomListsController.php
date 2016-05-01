<?php

class RandomListsController extends \BaseController {

	/**
	 * Display a listing of RandomLists
	 *
	 * @return Response
	 */
	public function index()
	{
		$RandomLists = RandomList::all();

		return View::make('RandomLists.index', compact('RandomLists'));
	}

	/**
	 * Show the form for creating a new RandomList
	 *
	 * @return Response
	 */
	public function create($challenge_id)
	{
		$challenge = Challenge::with('random_lists')->findOrFail($challenge_id);
		$order = $challenge->random_lists->max('display_order') + 1;

		return View::make('random_lists.create')
				   ->with(compact('challenge_id', 'order'));
	}

	/**
	 * Store a newly created RandomList in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), RandomList::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		RandomList::create($data);

		return 'true';
	}

	/**
	 * Display the specified RandomList.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$RandomList = RandomList::findOrFail($id);

		return View::make('RandomLists.show', compact('RandomList'));
	}

	/**
	 * Show the form for editing the specified RandomList.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$random_list = RandomList::find($id);

		return View::make('random_lists.edit', compact('random_list'));
	}

	/**
	 * Update the specified RandomList in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$RandomList = RandomList::findOrFail($id);

		$validator = Validator::make($data = Input::all(), RandomList::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$RandomList->update($data);

		return 'true';
	}

	/**
	 * Remove the specified RandomList from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		RandomList::destroy($id);

		return Redirect::back();
	}

}
