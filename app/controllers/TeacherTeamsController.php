<?php

class TeacherTeamsController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		Breadcrumbs::addCrumb('Teams', 'index');
		$school_id = Usermeta::getSchoolId();
		$school = Schools::find($school_id);
		$invoice = Wp_invoice::with('challenge_division')->where('user_id', Auth::user()->ID)->first();
		$paid = $invoice->paid==1 ? 'Paid' : 'Unpaid';

		$teams = Team::where('school_id', $school_id)->get();

        return View::make('teacher.teams.index', compact('school_id', 'teams', 'school', 'invoice', 'paid'));
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
		$school_name = Usermeta::getSchoolName();

        return View::make('teacher.teams.create', compact('divisions', 'school_name'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$input['school_name'] = Usermeta::getSchoolName();
		$validation = Validator::make($input, Team::$rules);

		if ($validation->passes())
		{
			Team::create($input);

			return Redirect::route('teacher.teams.index');
		}

		return Redirect::route('teacher.teams.create')
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
        return View::make('teacher.teams.show');
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
		$team = Team::find($id);

		if (is_null($team))
		{
			return Redirect::route('teacher.teams.index');
		}

		$divisions = Division::longname_array();

		return View::make('teacher.teams.edit', compact('team'))
				   ->with('divisions', $divisions);
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
		$input['school_name'] = Usermeta::getSchoolName();
		$validation = Validator::make($input, Team::$rules);

		if ($validation->passes())
		{
			$team = Team::find($id);
			$team->update($input);

			return Redirect::route('teacher.teams.index', $id);
		}

		return Redirect::route('teacher.teams.edit', $id)
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

		return Redirect::route('teacher.teams.index');
	}

}
