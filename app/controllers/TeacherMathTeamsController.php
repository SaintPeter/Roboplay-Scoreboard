<?php

class TeacherMathTeamsController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		Breadcrumbs::addCrumb('Manage Math Teams', 'teacher');
		$school_id = Usermeta::getSchoolId();
		$school = Schools::find($school_id);
		$invoice = Wp_invoice::with('challenge_division')->where('user_id', Auth::user()->ID)->first();

		if(!isset($invoice)) {
			return View::make('error', [ 'message' => 'No invoice found for this School']);
		}

		$paid = $invoice->paid==1 ? 'Paid' : 'Unpaid';

		$math_teams = MathTeam::with('school')
					->where('school_id', $school_id)
					->where('division_id', $invoice->division_id)
					->get();

		View::share('title', 'Manage Math Teams');
        return View::make('teacher.math_teams.index', compact('school_id', 'math_teams', 'school', 'invoice', 'paid'));
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
		$competitions = MathCompetition::with(
									[ 'divisions' => function($q) {
											return $q->orderby('display_order');
										} ] )
									->get();
		$division_list = [];
		foreach($competitions as $competition) {
			foreach($competition->divisions as $division) {
				$division_list[$competition->name][$division->id] = $division->name;
			}
		}

		// Ethnicity List Setup
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->lists('name','id'));

		View::share('title', 'Add Team - ' . $school->name);
        return View::make('teacher.math_teams.create', compact('school','ethnicity_list', 'division_list'));
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
		$teacher = Wp_user::with('usermeta')->find(Auth::user()->ID);
		$school_id = $teacher->getMeta('wp_school_id');
		$input['teacher_id'] = Auth::user()->ID;
		$input['year'] = Carbon\Carbon::now()->year;

		$students = Input::get('students');

		$math_teamErrors = Validator::make($input, MathTeam::$rules);

		if ($math_teamErrors->passes())
		{
			if(!empty($students)) {
				$students_pass = true;
				foreach ($students as $index => $student) {
				 	 $student_rules = Student::$rules;
					if(array_key_exists('id', $student)) {
						$student_rules['ssid'] .= ',' . $student['id'];
					}
				 	$studentErrors[$index] = Validator::make($student, $student_rules);
				 	 if($studentErrors[$index]->fails()) {
				 	 	$students_pass = false;
				 	 	$students[$index]['errors'] = $studentErrors[$index]->messages()->all();
				 	}
				}

				if($students_pass) {
					$newTeam = MathTeam::create($input);
					$sync_list = [];

					foreach ($students as $index => &$student) {
						$student['year'] = Carbon\Carbon::now()->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
							$student['school_id'] = $school_id;
							$student['teacher_id'] = Auth::user()->ID;
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$newTeam->students()->sync($sync_list);
					return Redirect::route('teacher.index');
				} else {
					return Redirect::route('teacher.math_teams.create')
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just create the math_team
				MathTeam::create($input);
				return Redirect::route('teacher.index');
			}
		}

		return Redirect::route('teacher.math_teams.create')
			->withInput(Input::except('students'))
			->with('students', $students)
			->withErrors($math_teamErrors)
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
		$math_team = MathTeam::with('students')->find($id);

		// Create a list of Divisions to choose from
		$competitions = MathCompetition::with(
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
			$students = $math_team->students;
		} else {
			// On subsequent loads or errors, use the sessions variable
			$students = [];
		}
		$index = -1;


		if (is_null($math_team))
		{
			return Redirect::route('teacher.index');
		}

		$divisions = Division::longname_array();

		return View::make('teacher.math_teams.edit', compact('math_team','students', 'division_list', 'ethnicity_list', 'index'))
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
		$teacher = Wp_user::with('usermeta')->find(Auth::user()->ID);
		$school_id = $teacher->getMeta('wp_school_id');
		$input['teacher_id'] = Auth::user()->ID;
		$input['year'] = Carbon\Carbon::now()->year;

		$students = Input::get('students');

		$math_teamValidation = Validator::make($input, MathTeam::$rules);

		if ($math_teamValidation->passes())
		{
			if(!empty($students)) {
				$students_pass = true;
				foreach ($students as $index => $student) {
				 	 $student_rules = Student::$rules;
					if(array_key_exists('id', $student)) {
						$student_rules['ssid'] .= ',' . $student['id'];
					}
				 	$studentErrors[$index] = Validator::make($student, $student_rules);
				 	 if($studentErrors[$index]->fails()) {
				 	 	$students_pass = false;
				 	 	$students[$index]['errors'] = $studentErrors[$index]->messages()->all();
				 	}
				}

				if($students_pass) {
					$math_team = MathTeam::find($id);
					$math_team->update($input);

					foreach ($students as $index => &$student) {
						$student['year'] = Carbon\Carbon::now()->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
							$student['school_id'] = $school_id;
							$student['teacher_id'] = Auth::user()->ID;
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$math_team->students()->sync($sync_list);
					return Redirect::route('teacher.index');
				} else {
					return Redirect::route('teacher.math_teams.edit', $id)
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just update the math_team
				$math_team = MathTeam::find($id);
				$math_team->update($input);
				return Redirect::route('teacher.index');
			}

			return Redirect::route('teacher.index');
		}

		return Redirect::route('teacher.math_teams.edit', $id)
			->withInput()
			->withErrors($math_teamValidation)
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
		MathTeam::find($id)->delete();

		return Redirect::route('teacher.index');
	}

}
