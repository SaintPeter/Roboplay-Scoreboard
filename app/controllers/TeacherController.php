<?php

use Illuminate\Routing\Controller;
use Carbon\Carbon;

class TeacherController extends BaseController {

	public function __construct()
	{
		parent::__construct();

		Breadcrumbs::addCrumb('Manage Teams', 'teacher');
	}


	/**
	 * Display a listing of the resource.
	 * GET /teacher
	 *
	 * @return Response
	 */
	public function index()
	{
		$invoice = Wp_invoice_table::where('invoice_type_id', 16)
								    ->where('user_id', Auth::user()->ID)
								    ->with('invoice_data','user','user.usermeta')
								    ->first();

		if(!isset($invoice)) {
			return View::make('error', [ 'message' => 'No invoice found for this School']);
		}

		$school_id = $invoice->user->getMeta('wp_school_id',0);

		if($school_id == 0) {
			return View::make('error', [ 'message' => 'School Id not set']);
		}

		$school = Schools::with('district')->find($school_id);

		$paid = $invoice->paid==1 ? 'Paid' : 'Unpaid';

		$teams = Team::with('school','students')
					->where('teacher_id', Auth::user()->ID)
					->where('year', Carbon::now()->year)
					->get();

	 	//dd($teams);

		$videos = Video::with('school', 'vid_division')
					->where('teacher_id', Auth::user()->ID)
					->where('year', Carbon::now()->year)
					->get();

		$math = new Illuminate\Database\Eloquent\Collection;

		View::share('title', 'Manage Teams');
        return View::make('teacher.index', compact('invoice', 'teams', 'videos', 'math', 'school', 'paid'));

	}

	// Returns a view with a new blank student form
	public function ajax_blank_student($index) {
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->lists('name','id'));
		return View::make('students.partial.create_empty')->with(compact('index', 'ethnicity_list'));
	}

	// Returns a view with a table of unattached students for a given type
	public function ajax_student_list($type, $teacher_id = null) {
		$current_students = Input::get('current_students', []);

		// If the teacher_id is not set, use the current user's id
		if(!$teacher_id) {
			$teacher_id = Auth::user()->ID;
		}

		//  Get the school the teacher teaches at
		$school_id = Wp_user::with('usermeta')->find($teacher_id)->getMeta('wp_school_id');

		// Find all students from that school OR from that teacher
		$student_query = Student::with('teacher','teacher.usermeta')
						->where('school_id', $school_id)
						->orWhere('teacher_id', $teacher_id);

		// Select students where they are not attached to the given type
		switch($type) {
			case 'teams':
				$student_query = $student_query->has('teams', '=', 0);
				break;
			case 'videos':
				$student_query = $student_query->has('videos', '=', 0);
				break;
			case 'maths':
				$student_query = $student_query->has('maths', '=', 0);
				break;
		}

		// Ignore students who are already on the current form
		if(count($current_students) > 0) {
			$student_query = $student_query->whereNotIn('id', $current_students);
		}

		// Run query
		$student_list = $student_query->get();


		// Get teacher names
		$students = [];
		foreach($student_list as $student) {
			$students[$student->teacher->getName()][$student->id] = $student->fullName();
		}

		//dd(DB::getQueryLog());

		return View::make('students.partial.list')->with(compact('students'));
	}

	// Return the forms for editable students based on a POSTed list
	public function ajax_load_students($index) {
		$student_list = Input::get('students');
		$students = Student::whereIn('id', $student_list)->get();

		// Ethnicity List Setup
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->lists('name','id'));

		return View::make('students.partial.edit_list')->with(compact('students', 'ethnicity_list', 'index'));
	}

	public function ajax_import_students_csv() {
		$field_names = [
			"First Name" => 'first_name',
			"Middle/Nick Name" => 'middle_name',
			"Last Name" => 'last_name',
			"SSID" => 'ssid',
			"Gender" => 'gender',
			"Ethnicity" => 'ethnicity_id',
			"Grade" => 'grade',
			"E-mail" => 'email' ];

		$ethnicity_decode = Ethnicity::all()->lists('id', 'name');
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->lists('name','id'));

		$csv = new parseCSV(Input::file('csv_file')->getRealPath());
		$rawData = $csv->data;

		// Index doesn't matter because things get renumbered in the view
		$index = 0;

		// Clean up/translate data, fix field names
		foreach($rawData as $student) {
			if(!empty($student['First Name'])) {
				foreach($field_names as $import_field => $proper_field) {
					if(array_key_exists($import_field, $student)) {
						if($import_field == 'Ethnicity') {
							if(array_key_exists($student[$import_field], $ethnicity_decode)) {
								$students[$index][$proper_field] = $ethnicity_decode[$student[$import_field]];
							} else {
								$students[$index][$proper_field]  = "";
							}
						} else {
							$students[$index][$proper_field]  = $student[$import_field];
						}
					} else {
						// Set a default value
						$students[$index][$proper_field] = "";
					}
				}
				$students[$index]['nickname'] = 0;
				$index++;
			}
		}

		$index = Input::get('index');

		return View::make('students.partial.edit_list')->with(compact('students', 'ethnicity_list', 'index'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /teacher/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /teacher
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /teacher/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /teacher/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /teacher/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /teacher/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}