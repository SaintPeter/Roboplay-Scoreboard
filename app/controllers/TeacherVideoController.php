<?php

class TeacherVideoController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		Breadcrumbs::addCrumb('Videos', 'teacher/teams');
	
		$school_id = Usermeta::getSchoolId();
		$school = Schools::find($school_id);
		$invoice = Wp_invoice::with('challenge_division')->where('user_id', Auth::user()->ID)->first();
		
		if(!isset($invoice)) {
			return View::make('error', [ 'message' => 'No invoice found for this user.']);
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
		$invoice = Wp_invoice::with('challenge_division')->where('user_id', Auth::user()->ID)->first();
		$input['division_id'] = $invoice->challenge_division->id;
		
		$validation = Validator::make($input, Video::$rules);

		if ($validation->passes())
		{
			$this->video->create($input);

			return Redirect::route('teacher.videos.index');
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
		$video = $this->video->findOrFail($id);

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
		$video = $this->video->with('vid_division')->find($id);

		if (is_null($video))
		{
			return Redirect::route('teacher.videos.index');
		}

		return View::make('teacher.videos.edit', compact('video','vid_divisions'));
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
		$invoice = Wp_invoice::with('challenge_division')->where('user_id', Auth::user()->ID)->first();
		$input['division_id'] = $invoice->challenge_division->id;
		
		$validation = Validator::make($input, Video::$rules);

		if ($validation->passes())
		{
			$video = $this->video->find($id);
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
		$this->video->find($id)->delete();

		return Redirect::route('teacher.videos.index');
	}
}
