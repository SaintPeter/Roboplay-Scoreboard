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
		$videos = Video::with('vid_division', 'school', 'school.district', 'school.district.county')->get();

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
		$vid_divisions = Vid_division::longname_array();

		return View::make('videos.create', compact('vid_divisions'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = array_except(Input::all(), ['select_county', 'select_district' ]);
		// Skip check on video
		$rules = Video::$rules;
		unset($rules['yt_code']);
		$validation = Validator::make($input, $rules);

		if ($validation->passes())
		{
			Video::create($input);

			return Redirect::route('videos.index');
		}

		return Redirect::route('videos.create')
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
		Breadcrumbs::addCrumb('Show Video', 'videos');
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
		$video = Video::with('vid_division', 'school', 'school.district', 'school.district.county')->find($id);
		$vid_divisions = Vid_division::longname_array();

		if (is_null($video))
		{
			return Redirect::route('videos.index');
		}

		return View::make('videos.edit', compact('video', 'vid_divisions'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = array_except(Input::all(), ['_method', 'select_county', 'select_district' ]);

		// Skip check on video
		$rules = Video::$rules;
		unset($rules['yt_code']);
		$validation = Validator::make($input, $rules);

		if ($validation->passes())
		{
			$video = Video::find($id);
			$video->update($input);

			return Redirect::route('videos.show', $id);
		}

		return Redirect::route('videos.edit', $id)
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

		return Redirect::route('videos.index');
	}
}
