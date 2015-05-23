<?php

class MathDivisionsController extends \BaseController {

	public function __construct()
	{
		parent::__construct();
		Breadcrumbs::addCrumb('Math Divisions', 'math_divisions');
	}

	/**
	 * Display a listing of math_divisions
	 *
	 * @return Response
	 */
	public function index()
	{
		View::share('title', 'Math Divisions');
		$math_divisions = MathDivision::with('challenges')->get();

		return View::make('math_divisions.index', compact('math_divisions'));
	}

	/**
	 * Show the form for creating a new math_division
	 *
	 * @return Response
	 */
	public function create()
	{
		Breadcrumbs::addCrumb('Add Math Division', 'create');
		View::share('title', 'Add Math Division');
		$math_competitions = MathCompetition::lists('name','id');

		return View::make('math_divisions.create',compact('math_competitions'));
	}

	/**
	 * Store a newly created math_division in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), MathDivision::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		MathDivision::create($data);

		return Redirect::route('math_divisions.index');
	}

	/**
	 * Display the specified math_division.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		Breadcrumbs::addCrumb('Show Math Division', 'create');
		View::share('title', 'Show Math Division');
		$math_division = MathDivision::with('challenges')->findOrFail($id);

		return View::make('math_divisions.show', compact('math_division'));
	}

	/**
	 * Show the form for editing the specified math_division.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		Breadcrumbs::addCrumb('Edit Math Division', 'create');
		View::share('title', 'Edit Math Division');

		$math_competitions = MathCompetition::lists('name','id');

		$math_division = MathDivision::find($id);

		return View::make('math_divisions.edit', compact('math_division','math_competitions'));
	}

	/**
	 * Update the specified math_division in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$math_division = MathDivision::findOrFail($id);

		$validator = Validator::make($data = Input::except('_method'), MathDivision::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$math_division->update($data);

		return Redirect::route('math_divisions.index');
	}

	/**
	 * Remove the specified math_division from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		MathDivision::destroy($id);

		return Redirect::route('math_divisions.index');
	}

}
