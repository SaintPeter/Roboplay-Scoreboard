<?php

class TeacherTeamsController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		Breadcrumbs::addCrumb('Manage Challenge Teams', 'teacher');
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
		Breadcrumbs::addCrumb('Manage Teams and Videos', 'teacher');
		Breadcrumbs::addCrumb('Add Video', 'create');
		$school_id = Usermeta::getSchoolId();
		$school = Schools::find($school_id);

		// Create a list of Divisions to choose from
		$competitions = Competition::where('active', true)->with(
									[ 'divisions' => function($q) {
											return $q->orderby('display_order');
										} ] )
									->get();

		foreach($competitions as $competition) {
			foreach($competition->divisions as $division) {
				$division_list[$competition->name][$division->id] = $division->name;
			}
		}

		// Ethnicity List Setup
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->lists('name','id'));

		View::share('title', 'Add Team - ' . $school->name);
        return View::make('teacher.teams.create', compact('school','ethnicity_list', 'division_list'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('students');
		$input['school_id'] = Usermeta::getSchoolId();
		$input['teacher_id'] = Auth::user()->ID;
		$input['year'] = Carbon\Carbon::now()->year;

		$students = Input::get('students');

		//dd($students);

		$teamErrors = Validator::make($input, Team::$rules);

		if ($teamErrors->passes())
		{
			if(!empty($students)) {
				$students_pass = true;
				foreach ($students as $index => $student) {
				 	 $studentErrors[$index] = Validator::make($student, Student::$rules);
				 	 if($studentErrors[$index]->fails()) {
				 	 	$students_pass = false;
				 	 	$students[$index]['errors'] = $studentErrors[$index]->messages()->all();
				 	}
				}

				if($students_pass) {
					$newTeam = Team::create($input);
					$sync_list = [];

					foreach ($students as $index => &$student) {
						$student['teacher_id'] = Auth::user()->ID;
						$student['year'] = Carbon\Carbon::now()->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$newTeam->students()->sync($sync_list);
					return Redirect::route('teacher.index');
				} else {
					return Redirect::route('teacher.teams.create')
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just create the team
				Team::create($input);
				return Redirect::route('teacher.index');
			}
		}

		return Redirect::route('teacher.teams.create')
			->withInput(Input::except('students'))
			->with('students', $students)
			->withErrors($teamErrors)
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
		Breadcrumbs::addCrumb('Manage Teams and Videos', 'teacher');
		Breadcrumbs::addCrumb('Edit Team', $id);
		View::share('title', 'Edit Team');
		$team = Team::with('students')->find($id);

		// Create a list of Divisions to choose from
		$competitions = Competition::where('active', true)->with(
									[ 'divisions' => function($q) {
											return $q->orderby('display_order');
										} ] )
									->get();

		foreach($competitions as $competition) {
			foreach($competition->divisions as $division) {
				$division_list[$competition->name][$division->id] = $division->name;
			}
		}

		// Student Setup
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->lists('name','id'));
		if(!Session::has('students')) {
			// On first load we populate the form from the DB
			$students = $team->students;
		} else {
			// On subsequent loads or errors, use the sessions variable
			$students = [];
		}
		$index = -1;


		if (is_null($team))
		{
			return Redirect::route('teacher.index');
		}

		$divisions = Division::longname_array();

		return View::make('teacher.teams.edit', compact('team','students', 'division_list', 'ethnicity_list', 'index'))
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
		$input = Input::except('_method', 'students');
		$input['school_id'] = Usermeta::getSchoolId();
		$input['teacher_id'] = Auth::user()->ID;
		$input['year'] = Carbon\Carbon::now()->year;

		$students = Input::get('students');

		$teamValidation = Validator::make($input, Team::$rules);

		if ($teamValidation->passes())
		{
			if(!empty($students)) {
				$students_pass = true;
				foreach ($students as $index => $student) {
				 	 $studentErrors[$index] = Validator::make($student, Student::$rules);
				 	 if($studentErrors[$index]->fails()) {
				 	 	$students_pass = false;
				 	 	$students[$index]['errors'] = $studentErrors[$index]->messages()->all();
				 	}
				}

				if($students_pass) {
					$team = Team::find($id);
					$team->update($input);

					foreach ($students as $index => &$student) {
						$student['teacher_id'] = Auth::user()->ID;
						$student['year'] = Carbon\Carbon::now()->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$team->students()->sync($sync_list);
					return Redirect::route('teacher.index');
				} else {
					return Redirect::route('teacher.teams.edit', $id)
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just update the team
				$team = Team::find($id);
				$team->update($input);
				return Redirect::route('teacher.index');
			}

			return Redirect::route('teacher.index');
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

		return Redirect::route('teacher.index');
	}

}
