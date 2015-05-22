<?php

class MathCompetitionsController extends \BaseController {

	public function __construct(Vid_competition $vid_competition)
	{
		parent::__construct();
		Breadcrumbs::addCrumb('Math Competitions', 'math_competitions');
	}


	/**
	 * Display a listing of mathcompetitions
	 *
	 * @return Response
	 */
	public function index()
	{
		View::share('title', 'Math Competitions');

		$math_competitions = MathCompetition::all();

		return View::make('math_competitions.index', compact('math_competitions'));
	}

	/**
	 * Show the form for creating a new mathcompetition
	 *
	 * @return Response
	 */
	public function create()
	{
		Breadcrumbs::addCrumb('Add Math Competition', 'create');
		View::share('title', 'Add Math Competition');
		return View::make('math_competitions.create');
	}

	/**
	 * Store a newly created mathcompetition in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), MathCompetition::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		MathCompetition::create($data);

		return Redirect::route('math_competitions.index');
	}

	/**
	 * Display the specified mathcompetition.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		Breadcrumbs::addCrumb('Show Math Competition', 'show');
		View::share('title', 'Show Math Competition');
		$math_competition = MathCompetition::findOrFail($id);

		return View::make('math_competitions.show', compact('math_competition'));
	}

	/**
	 * Show the form for editing the specified mathcompetition.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		Breadcrumbs::addCrumb('Edit Math Competition', 'edit');
		View::share('title', 'Edit Math Competition');
		$math_competition = MathCompetition::find($id);

		return View::make('math_competitions.edit', compact('math_competition'));
	}

	/**
	 * Update the specified mathcompetition in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$math_competition = MathCompetition::findOrFail($id);

		$validator = Validator::make($data = Input::all(), MathCompetition::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$math_competition->update($data);

		return Redirect::route('math_competitions.index');
	}

	/**
	 * Remove the specified mathcompetition from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		MathCompetition::destroy($id);

		return Redirect::route('math_competitions.index');
	}

}
