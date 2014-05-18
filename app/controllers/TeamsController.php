<?php

class TeamsController extends BaseController {

	public function __construct()
	{
		parent::__construct();
		Breadcrumbs::addCrumb('Teams', 'teams');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$teams = Team::with('division', 'school', 'school.district', 'school.district.county')->get();

		View::share('title', 'Teams');
		return View::make('teams.index', compact('teams'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		Breadcrumbs::addCrumb('Add Team', 'create');
		View::share('title', 'Add Team');
		$divisions = Division::longname_array();

		return View::make('teams.create')
				   ->with('divisions', $divisions);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = array_except(Input::all(), ['_method', 'select_county', 'select_district' ]);

		$validation = Validator::make($input, Team::$rules);

		if ($validation->passes())
		{
			Team::create($input);

			return Redirect::route('teams.index');
		}

		return Redirect::route('teams.create')
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		Breadcrumbs::addCrumb('Show Team', $id);
		View::share('title', 'Show Team');
		$team = Team::with('school', 'school.district', 'school.district.county')->findOrFail($id);

		return View::make('teams.show', compact('team'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		Breadcrumbs::addCrumb('Edit Team', $id);
		View::share('title', 'Edit Team');
		$team = Team::with('school', 'school.district', 'school.district.county')->find($id);

		$starting_school = isset($team->school) ? $team->school->school_id : 0;
		$starting_district = isset($team->school) ? $team->school->district->district_id : 0;
		$starting_county = isset($team->school) ? $team->school->district->county->county_id : 0;

		if (is_null($team))
		{
			return Redirect::route('teams.index');
		}

		$divisions = Division::longname_array();
		$vid_divisions = Vid_division::longname_array();

		return View::make('teams.edit', compact('team', 'starting_county', 'starting_district', 'starting_school'))
				   ->with('divisions', $divisions)
				   ->with('vid_divisions', $vid_divisions);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = array_except(Input::all(), ['_method', 'select_county', 'select_district' ]);

		$validation = Validator::make($input, Team::$rules);

		if ($validation->passes())
		{
			$team = Team::find($id);
			$team->update($input);

			return Redirect::route('teams.show', $id);
		}

		return Redirect::route('teams.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Team::find($id)->delete();

		Session::forget('currentCompetition');
		Session::forget('currentDivision');
		Session::forget('currentTeam');

		return Redirect::route('teams.index');
	}

}
