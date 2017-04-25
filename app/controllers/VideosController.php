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
		// Selected year set in filters.php -> App::before()
		$year = Session::get('year', false);

		$video_query = Video::with('vid_division',
		                           'school', 'students', 'teacher', 'teacher.usermeta',
		                           'awards')
							->orderBy('year', 'desc')
							->orderBy('teacher_id');

		if($year) {
			$video_query = $video_query->where('year', $year);
		}

		$videos = $video_query->get();

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

		// Student Setup
		$students = [];

        // Video Awards List Setup
	    $awards_list = VideoAward::all()->lists('name', 'id');

		$index = 0;
		View::share('index', $index);
		return View::make('videos.create', compact('vid_divisions', 'awards_list', 'teacher_list', 'ethnicity_list', 'students'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except(['students', 'awards' ]);
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
							$student['teacher_id'] = Input::get('teacher_id',Auth::user()->ID);
							$student['school_id'] = $input['school_id'];
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$newvideo->students()->sync($sync_list);
					$newvideo->awards()->sync(Input::get('awards', []));
					return Redirect::route('videos.index');
				} else {
					return Redirect::route('videos.create')
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just create the team
				$newvideo = Video::create($input);
				$newvideo->awards()->sync(Input::get('awards', []));
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
		$video = Video::with('awards')->findOrFail($id);

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
		$video = Video::with('teacher','vid_division','school','awards')->find($id);
		$vid_divisions = Vid_division::longname_array();

		$teacher_list = [];
		$this->populate_teacher_list($teacher_list);

		// Ethnicity List Setup
		$ethnicity_list = array_merge([ 0 => "- Select Ethnicity -" ], Ethnicity::all()->lists('name','id'));

        // Video Awards List Setup
	    $awards_list = VideoAward::all()->lists('name', 'id');
	    $awards_selected = $video->awards->lists('id');

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
		return View::make('videos.edit', compact('video', 'vid_divisions', 'awards_list', 'awards_selected' , 'teacher_list', 'ethnicity_list', 'students'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except('_method', 'students', 'awards' );
		$students = Input::get('students');
		// Skip check on video
		$rules = Video::$rules;
		unset($rules['yt_code']);
		$input['school_id'] = Wp_user::find(Input::get('teacher_id'))->getMeta('wp_school_id', 0);
		// $input['year'] = Carbon\Carbon::now()->year;  // Don't overwrite the current year

		$videoErrors = Validator::make($input, $rules);

		if ($videoErrors->passes())
		{
			if(!empty($students)) {
				$students_pass = true;
				//dd($students);
				foreach ($students as $index => $student) {
				 	$student_rules = Student::$rules;
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
							$student['teacher_id'] = Input::get('teacher_id',Auth::user()->ID);
							$student['school_id'] = $input['school_id'];
							$newStudent = Student::create($student);
						}
						$sync_list[] = $newStudent->id;
					}
					$video->students()->sync($sync_list);
					$video->awards()->sync(Input::get('awards', []));
					return Redirect::route('videos.index');
				} else {
					return Redirect::route('videos.edit', $id)
						->withInput(Input::except('students'))
						->with('students', $students)
						->with('message', 'There were validation errors.');
				}
			} else {
				// No students, just update the video
				$video = video::find($id);
				$video->update($input);
				$video->awards()->sync(Input::get('awards', []));
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
