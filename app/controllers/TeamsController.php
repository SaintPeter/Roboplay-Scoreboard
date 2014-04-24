<?php

class TeamsController extends BaseController {

	/**
	 * Team Repository
	 *
	 * @var Team
	 */
	protected $team;

	public function __construct(Team $team)
	{
		parent::__construct();
		$this->team = $team;
		Breadcrumbs::addCrumb('Teams', 'teams');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$teams = $this->team->with('division')->get();

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
		$divisions = Division::longname_array();
		$vid_divisions = Vid_division::longname_array();

		return View::make('teams.create')
				   ->with('divisions', $divisions)
				   ->with('vid_divisions', $vid_divisions);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Team::$rules);

		if ($validation->passes())
		{
			$this->team->create($input);

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
		$team = $this->team->findOrFail($id);

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
		$team = $this->team->find($id);

		if (is_null($team))
		{
			return Redirect::route('teams.index');
		}

		$divisions = Division::longname_array();
		$vid_divisions = Vid_division::longname_array();

		return View::make('teams.edit', compact('team'))
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
		$input = array_except(Input::all(), '_method');
		$validation = Validator::make($input, Team::$rules);

		if ($validation->passes())
		{
			$team = $this->team->find($id);
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
		$this->team->find($id)->delete();

		Session::forget('currentCompetition');
		Session::forget('currentDivision');
		Session::forget('currentTeam');

		return Redirect::route('teams.index');
	}

}
