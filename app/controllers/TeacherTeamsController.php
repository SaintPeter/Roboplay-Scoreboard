<?php

class TeacherTeamsController extends BaseController {

	public function index()
	{
	    // Replaced by combined interface
	    return Redirect::route('teacher.index');
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

        // Get the most recent competition year with comptition divisisons
	    $comp_year = CompYear::orderBy('year', 'desc')
	                         ->with([ 'divisions' => function($q) {
										return $q->orderby('display_order');
									}])
							->first();

        $division_list[0] = "- Select Division -";
        foreach($comp_year->divisions as $division) {
            $division_list[$division->competition->name][$division->id] = $division->name;
        }

		// Ethnicity List Setup
		$ethnicity_list = [ 0 => "- Select Ethnicity -" ] + Ethnicity::all()->lists('name','id');

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
		$teacher = Wp_user::with('usermeta')->find(Auth::user()->ID);
		$school_id = $teacher->getMeta('wp_school_id');
		$input['teacher_id'] = Auth::user()->ID;
		$input['year'] = Carbon\Carbon::now()->year;

		$students = Input::get('students');

		$teamErrors = Validator::make($input, Team::$rules);

		if ($teamErrors->passes())
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
					$newTeam = Team::create($input);
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

		// Get the most recent competition year with comptition divisisons
	    $comp_year = CompYear::orderBy('year', 'desc')
	                         ->with([ 'divisions' => function($q) {
										return $q->orderby('display_order');
									}, 'divisions.competition'])
							->first();

        $division_list[0] = "- Select Division -";
        foreach($comp_year->divisions as $division) {
            $division_list[$division->competition->name][$division->id] = $division->name;
        }

		// Student Setup
		$ethnicity_list = [ 0 => "- Select Ethnicity -" ] + Ethnicity::all()->lists('name','id');
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
		$teacher = Wp_user::with('usermeta')->find(Auth::user()->ID);
		$school_id = $teacher->getMeta('wp_school_id');
		$input['teacher_id'] = Auth::user()->ID;
		$input['year'] = Carbon\Carbon::now()->year;

		$students = Input::get('students');

		$teamValidation = Validator::make($input, Team::$rules);

		if ($teamValidation->passes())
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
					$team = Team::find($id);
					$team->audit = 0;
					$team->update($input);

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
				$team->audit = 0;
				$team->update($input);
				return Redirect::route('teacher.index');
			}

			return Redirect::route('teacher.index');
		}

		return Redirect::route('teacher.teams.edit', $id)
			->withInput()
			->withErrors($teamValidation)
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
