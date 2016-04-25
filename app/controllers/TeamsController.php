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
		// Selected year set in filters.php -> App::before()
		$year = Session::get('year', false);

		if($year) {
			$teams = Team::where('year', $year)->with('division', 'school', 'school.district', 'school.district.county', 'teacher', 'teacher.usermeta', 'students')->get();
		} else {
			$teams = Team::with('division', 'school', 'school.district', 'school.district.county', 'teacher', 'teacher.usermeta', 'students')
						->orderBy('year', 'desc')
						->get();
		}

		$division_list = Division::longname_array();

		View::share('title', 'Teams');
		return View::make('teams.index', compact('teams', 'division_list'));
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
		$division_list = Division::longname_array();

		$teacher_list = [];
		$this->populate_teacher_list($teacher_list);

		// Ethnicity List Setup
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->lists('name','id'));
		// Student Setup
		$students = [];

		View::share('index', 0);

		return View::make('teams.create')
				   ->with(compact('division_list', 'teacher_list', 'ethnicity_list', 'students'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('_method', 'students');
		$input['year'] = Carbon\Carbon::now()->year;
		$input['school_id'] = Wp_user::find(Input::get('teacher_id'))->getMeta('wp_school_id', 0);

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
							$student['teacher_id'] = Input::get('teacher_id',Auth::user()->ID);
							$student['school_id'] = $input['school_id'];
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$newTeam->students()->sync($sync_list);
					return Redirect::route('teams.index');
				} else {
					return Redirect::route('teams.create')
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just create the team
				Team::create($input);
				return Redirect::route('teams.index');
			}
		}

		return Redirect::route('teams.create')
			->withInput(Input::except('students'))
			->with('students', $students)
			->withErrors($teamErrors)
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

		if (is_null($team))
		{
			return Redirect::route('teams.index');
		}

		$division_list = Division::longname_array();

		// Student Setup
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->lists('name','id'));
		if(!Session::has('students')) {
			// On first load we populate the form from the DB
			$students = $team->students;
		} else {
			// On subsequent loads or errors, use the sessions variable
			$students = [];
		}

		$teacher_list = [];
		$this->populate_teacher_list($teacher_list);

		View::share('index', -1);

		return View::make('teams.edit', compact('team','students', 'division_list', 'ethnicity_list', 'teacher_list'));
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
		$input['school_id'] = Wp_user::find(Input::get('teacher_id'))->getMeta('wp_school_id', 0);
		//$input['year'] = Carbon\Carbon::now()->year;

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
					$team->update($input);

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
					$team->students()->sync($sync_list);
					return Redirect::route('teams.index');
				} else {
					return Redirect::route('teams.edit', $id)
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just update the team
				$team = Team::find($id);
				$team->update($input);
				return Redirect::route('teams.index');
			}

			return Redirect::route('teams.index');
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

	public function populate_teacher_list(&$teacher_list) {
	    $invoices = Invoices::orderBy('year', 'asc')
	                        ->with('wp_user','wp_user.usermeta', 'school')
	                        ->get();

	    foreach($invoices as $invoice) {
	        if($invoice->school) {
	            $teacher_list[$invoice->user_id] = $invoice->wp_user->getNameProper() .
	                                               ' (' . $invoice->school->name . ')';
	        } else {
	            $teacher_list[$invoice->user_id] = $invoice->wp_user->getNameProper() .
	                                               ' (No School Set)';
	        }
	    }

		asort($teacher_list);
	}
}
