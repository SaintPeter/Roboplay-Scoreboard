<?php

class MathTeamsController extends \BaseController {

	public function __construct()
	{
		parent::__construct();
		Breadcrumbs::addCrumb('Math Teams', 'math_teams');
	}

	/**
	 * Display a listing of math_teams
	 *
	 * @return Response
	 */
	public function index()
	{
		// Selected year set in filters.php -> App::before()
		$year = Session::get('year', false);

		if($year) {
			$math_teams = MathTeam::where('year', $year)->with('division', 'school', 'school.district', 'school.district.county', 'teacher', 'teacher.usermeta', 'students')->get();
		} else {
			$math_teams = MathTeam::with('division', 'school', 'school.district', 'school.district.county', 'teacher', 'teacher.usermeta', 'students')
						->orderBy('year', 'desc')
						->get();
		}

		$division_list = Division::longname_array();

		View::share('title', 'Math Teams');

		return View::make('math_teams.index', compact('math_teams'));
	}

	/**
	 * Show the form for creating a new math_team
	 *
	 * @return Response
	 */
	public function create()
	{
		Breadcrumbs::addCrumb('Add Math Team', 'create');
		View::share('title', 'Add Math Team');
		$division_list = MathDivision::longname_array();

		$teacher_list = [];
		$this->populate_teacher_list($teacher_list);

		// Ethnicity List Setup
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->lists('name','id'));
		// Student Setup
		$students = [];

		View::share('index', 0);
		return View::make('math_teams.create',compact('division_list','teacher_list','ethnicity_list','students'));
	}

	/**
	 * Store a newly created math_team in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('_method', 'students');
		$input['year'] = Carbon\Carbon::now()->year;

		if(Input::get('teacher_id',0) != 0) {
			$input['school_id'] = Wp_user::find(Input::get('teacher_id'))->getMeta('wp_school_id', 0);
		}

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
							$student['teacher_id'] = Input::get('teacher_id',Auth::user()->ID);
							$student['school_id'] = $input['school_id'];
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$newTeam->students()->sync($sync_list);
					return Redirect::route('math_teams.index');
				} else {
					return Redirect::route('math_teams.create')
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just create the math_team
				MathTeam::create($input);
				return Redirect::route('math_teams.index');
			}
		}

		return Redirect::route('math_teams.create')
			->withInput(Input::except('students'))
			->with('students', $students)
			->withErrors($math_teamErrors)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Display the specified math_team.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$math_team = Mathteam::with('teacher', 'teacher.usermeta','students','school','school.district','school.district.county')->findOrFail($id);

		return View::make('math_teams.show', compact('math_team'));
	}

	/**
	 * Show the form for editing the specified math_team.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		Breadcrumbs::addCrumb('Edit Math Team', $id);
		View::share('title', 'Edit Math Team');
		$math_team = MathTeam::with('school', 'school.district', 'school.district.county')->find($id);

		if (is_null($math_team))
		{
			return Redirect::route('math_teams.index');
		}

		$division_list = MathDivision::longname_array();

		// Student Setup
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->lists('name','id'));
		if(!Session::has('students')) {
			// On first load we populate the form from the DB
			$students = $math_team->students;
		} else {
			// On subsequent loads or errors, use the sessions variable
			$students = [];
		}

		$teacher_list = [];
		$this->populate_teacher_list($teacher_list);

		View::share('index', -1);

		return View::make('math_teams.edit', compact('math_team','students', 'division_list', 'ethnicity_list', 'teacher_list'));
	}

	/**
	 * Update the specified math_team in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except('_method', 'students');
		$input['school_id'] = Wp_user::find(Input::get('teacher_id'))->getMeta('wp_school_id', 0);
		//$input['year'] = Carbon\Carbon::now()->year;

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
							$student['teacher_id'] = Input::get('teacher_id',Auth::user()->ID);
							$student['school_id'] = $input['school_id'];
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$math_team->students()->sync($sync_list);
					return Redirect::route('math_teams.index');
				} else {
					return Redirect::route('math_teams.edit', $id)
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just update the math_team
				$math_team = MathTeam::find($id);
				$math_team->update($input);
				return Redirect::route('math_teams.index');
			}

			return Redirect::route('math_teams.index');
		}

		return Redirect::route('math_teams.edit', $id)
			->withInput()
			->withErrors($validation)
			->with('message', 'There were validation errors.');
	}

	/**
	 * Remove the specified math_team from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		MathTeam::destroy($id);

		return Redirect::route('math_teams.index');
	}

	public function populate_teacher_list(&$teacher_list) {
		$teacher_ids = Wp_invoice_table::where('invoice_type_id', 16)->lists('user_id');
		$teachers = Wp_user::whereIn('ID', $teacher_ids)->get();
		$school_ids = Usermeta::whereIn('user_id', $teacher_ids)->where('meta_key', 'wp_school_id')->lists('meta_value','user_id');
		$school_list = Schools::whereIn('school_id', $school_ids)->lists('name', 'school_id');

		$teacher_list = [ 0 => '-- Select Teacher --'];
		foreach($teachers as $teacher) {
			if(array_key_exists($teacher->ID, $school_ids)) {
				$teacher_list[$teacher->ID] = $teacher->getNameProper() . " (" . $school_list[$school_ids[$teacher->ID]] . ")";
			} else {
			 	$teacher_list[$teacher->ID] = $teacher->getNameProper() . " (No School Set)";
			}
		}
		asort($teacher_list);
	}

}
