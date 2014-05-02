<?php

class TeacherVideoController extends BaseController {

	public function __construct() 
	{
		parent::__construct();
		
		Breadcrumbs::addCrumb('Videos', 'teacher/videos');
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$school_id = Usermeta::getSchoolId();
		$school = Schools::find($school_id);
		$invoice = Wp_invoice::with('vid_division')->where('school_id', $school_id)->first();
		
		if(!isset($invoice)) {
			return View::make('error', [ 'message' => 'No invoice found for this School.']);
		}
		
		$paid = $invoice->paid==1 ? 'Paid' : 'Unpaid';

		$videos = Video::with('school', 'vid_division')->where('school_id',$school_id)->get();

		return View::make('teacher.videos.index', compact('school', 'videos','invoice', 'paid'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		Breadcrumbs::addCrumb('Add Video', 'teacher/videos/create');
		$vid_divisions = Vid_division::longname_array();
		return View::make('teacher.videos.create',compact('vid_divisions'));
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
		$invoice = Wp_invoice::where('school_id', $input['school_id'])->first();
		$input['vid_division_id'] = $invoice->vid_division_id;
		
		$validation = Validator::make($input, Video::$rules);

		if ($validation->passes())
		{
			$video = Video::create($input);

			return Redirect::route('teacher.videos.show', $video->id);
		}

		return Redirect::route('teacher.videos.create')
			->withInput()
			->withErrors($validation)
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
		Breadcrumbs::addCrumb('Video Preview', 'teacher/videos/create');
		$video = Video::findOrFail($id);

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
		Breadcrumbs::addCrumb('Edit Video', 'teacher/videos/edit');
		$video = Video::with('vid_division')->find($id);

		if (is_null($video))
		{
			return Redirect::route('teacher.videos.index');
		}

		return View::make('teacher.videos.edit', compact('video'));
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
		$invoice = Wp_invoice::where('school_id', $input['school_id'])->first();
		$input['vid_division_id'] = $invoice->vid_division_id;
		
		$validation = Validator::make($input, Video::$rules);

		if ($validation->passes())
		{
			$video = Video::find($id);
			$video->update($input);

			return Redirect::route('teacher.videos.show', $id);
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

		return Redirect::route('teacher.videos.index');
	}
}
