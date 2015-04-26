<?php

class VideosController extends BaseController {

	/**
	 * Video Repository
	 *
	 * @var Video
	 */
	protected $video;

	public function __construct(Video $video)
	{
		parent::__construct();
		Breadcrumbs::addCrumb('Videos', 'videos');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Input::has('selected_year')) {
			$selected_year = Input::get('selected_year');
			if($selected_year == 'clear') {
				Session::forget('selected_year');
				$selected_year = false;
			} else {
				Session::put('selected_year', $selected_year);
			}
		} else {
			$selected_year = Session::get('selected_year', false);
		}

		if($selected_year) {
			$videos = Video::where('year', $selected_year)
							->with('vid_division', 'school', 'school.district', 'school.district.county')->get();
		} else {
			$videos = Video::with('vid_division', 'school', 'school.district', 'school.district.county')
							->orderBy('year', 'desc')
							->get();
		}

		View::share('title', 'Videos');
		return View::make('videos.index', compact('videos'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		Breadcrumbs::addCrumb('Add Video', 'videos');
		View::share('title', 'Add Video');
		$vid_divisions = Vid_division::longname_array();

		$teacher_list = [];
		$this->populate_teacher_list($teacher_list);

		// Ethnicity List Setup
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->lists('name','id'));

		$index = 0;
		View::share('index', $index);
		return View::make('videos.create', compact('vid_divisions', 'teacher_list', 'ethnicity_list'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = array_except(Input::all(), ['students' ]);
		// Skip check on video
		$rules = Video::$rules;
		unset($rules['yt_code']);
		$input['year'] = Carbon\Carbon::now()->year;
		$input['school_id'] = Wp_user::find(Input::get('teacher_id'))->getMeta('wp_school_id', 0);

		$students = Input::get('students');

		$videoErrors = Validator::make($input, $rules);

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
						$student['teacher_id'] = Input::get('teacher_id',0);
						$student['year'] = Carbon\Carbon::now()->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$newvideo->students()->sync($sync_list);
					return Redirect::route('videos.index');
				} else {
					return Redirect::route('videos.create')
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just create the team
				Video::create($input);
				return Redirect::route('teacher.index');
			}
		}

		return Redirect::route('videos.create')
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
		Breadcrumbs::addCrumb('Show Video', 'videos');
		View::share('title', 'Show Video');
		$video = Video::findOrFail($id);

		return View::make('videos.show', compact('video'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		Breadcrumbs::addCrumb('Edit Video', 'videos');
		View::share('title', 'Edit Video');
		$video = Video::with('vid_division', 'school', 'school.district', 'school.district.county')->find($id);
		$vid_divisions = Vid_division::longname_array();

		$teacher_list = [];
		$this->populate_teacher_list($teacher_list);

		// Ethnicity List Setup
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
			return Redirect::route('videos.index');
		}
		View::share('index', -1);
		return View::make('videos.edit', compact('video', 'vid_divisions', 'teacher_list', 'ethnicity_list', 'students'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except('_method', 'students' );

		// Skip check on video
		$rules = Video::$rules;
		unset($rules['yt_code']);
		$input['school_id'] = Wp_user::find(Input::get('teacher_id'))->getMeta('wp_school_id', 0);
		$input['year'] = Carbon\Carbon::now()->year;

		$videoErrors = Validator::make($input, $rules);

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
					$video = video::find($id);
					$video->update($input);

					foreach ($students as $index => &$student) {
						$student['teacher_id'] = Input::get('teacher_id', 0);
						$student['year'] = Carbon\Carbon::now()->year;
						if(array_key_exists('id', $student)) {
							$newStudent = Student::find($student['id']);
							$newStudent->update($student);
						} else {
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
				return Redirect::route('videos.index');
			}

			return Redirect::route('videos.index');
		}

		return Redirect::route('videos.edit', $id)
			->withInput()
			->withErrors($videoErrors)
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

		return Redirect::route('videos.index');
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
