<?php

class TeacherVideoController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// Replaced by combined teacher interface
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
		Breadcrumbs::addCrumb('Add Video', 'teacher/videos/create');
		$school_id = Usermeta::getSchoolId();
		$school = Schools::find($school_id);

		// Create a list of Divisions to choose from
		$competitions = Vid_competition::with(
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

		View::share('title', 'Create Video');
		return View::make('teacher.videos.create',compact('division_list', 'ethnicity_list'));
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

		$videoErrors = Validator::make($input, Video::$rules);

		if ($videoErrors->passes())
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
					$newvideo = video::create($input);
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
					$newvideo->students()->sync($sync_list);
					return Redirect::route('teacher.videos.show', $newvideo->id);
				} else {
					return Redirect::route('teacher.videos.create')
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just create the team
				$video = Video::create($input);
				return Redirect::route('teacher.videos.show', $video->id);
			}
		}

		return Redirect::route('teacher.videos.create')
			->withInput(Input::except('students'))
			->with('students', $students)
			->withErrors($videoErrors)
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
		View::share('title', 'Video Preview');
		Breadcrumbs::addCrumb('Manage Teams and Videos', 'teacher');
		Breadcrumbs::addCrumb('Video Preview', 'teacher/videos/create');
		$video = Video::with('school', 'school.district', 'school.district.county')->findOrFail($id);

		return View::make('teacher.videos.show', compact('video'));
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
		Breadcrumbs::addCrumb('Edit Video', $id);
		View::share('title', 'Edit Video');
		$video = Video::with('students')->find($id);

		// Create a list of Divisions to choose from
		$competitions = Vid_competition::with(
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
			$students = $video->students;
		} else {
			// On subsequent loads or errors, use the sessions variable
			$students = [];
		}
		$index = -1;


		if (is_null($video))
		{
			return Redirect::route('teacher.index');
		}

		$divisions = Division::longname_array();

		return View::make('teacher.videos.edit', compact('video','students', 'division_list', 'ethnicity_list', 'index'))
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

		$videoValidation = Validator::make($input, Video::$rules);

		if ($videoValidation->passes())
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
					$video = video::find($id);
					$video->update($input);

					foreach ($students as $index => &$student) {
						$student['year'] = Carbon\Carbon::now()->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
							$student['teacher_id'] = Auth::user()->ID;
							$student['school_id'] = $school_id;
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$video->students()->sync($sync_list);
					return Redirect::route('teacher.index');
				} else {
					return Redirect::route('teacher.videos.edit', $id)
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just update the video
				$video = video::find($id);
				$video->update($input);
				return Redirect::route('teacher.index');
			}

			return Redirect::route('teacher.index');
		}

		return Redirect::route('teacher.videos.edit', $id)
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
		Video::find($id)->delete();

		return Redirect::route('teacher.index');
	}
}
