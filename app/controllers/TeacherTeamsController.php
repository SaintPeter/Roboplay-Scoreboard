<?php

class TeacherTeamsController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		Breadcrumbs::addCrumb('Manage Challenge Teams', 'teacher/teams');
		$school_id = Usermeta::getSchoolId();
		$school = Schools::find($school_id);
		$invoice = Wp_invoice::with('challenge_division')->where('user_id', Auth::user()->ID)->first();

		if(!isset($invoice)) {
			return View::make('error', [ 'message' => 'No invoice found for this School']);
		}

		$paid = $invoice->paid==1 ? 'Paid' : 'Unpaid';

		$teams = Team::with('school')
					->where('school_id', $school_id)
					->where('division_id', $invoice->division_id)
					->get();

		View::share('title', 'Manage Challenge Teams');
        return View::make('teacher.teams.index', compact('school_id', 'teams', 'school', 'invoice', 'paid'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		Breadcrumbs::addCrumb('Manage Challenge Teams', 'teacher/teams');
		Breadcrumbs::addCrumb('Add Team', 'create');
		$school_id = Usermeta::getSchoolId();
		$school = Schools::find($school_id);

		View::share('title', 'Add Team - ' . $school->name);
        return View::make('teacher.teams.create', compact('school'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$input['school_id'] = Usermeta::getSchoolId();
		$invoice = Wp_invoice::with('challenge_division')->where('user_id', Auth::user()->ID)->first();
		$input['division_id'] = $invoice->challenge_division->id;

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
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		Breadcrumbs::addCrumb('Manage Challenge Teams', 'teacher/teams');
		Breadcrumbs::addCrumb('Edit Team', $id);
		View::share('title', 'Edit Team');
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
		$input['school_id'] = Usermeta::getSchoolId();
		$invoice = Wp_invoice::with('challenge_division')->where('user_id', Auth::user()->ID)->first();
		$input['division_id'] = $invoice->challenge_division->id;

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
