<?php

class ScoreVideosController extends \BaseController {

	/**
	 * Display a listing of Video_scores
	 *
	 * @return Response
	 */
	public function index()
	{
		$Video_scores = Video_scores::where('judge_id', Auth::user()->ID)->get();

		return View::make('Video_scores.index', compact('Video_scores'));
	}

	// Display the video for judging
	public function score($video_id)
	{

	}

	/**
	 * Show the form for creating a new Video_scores
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('Video_scores.create');
	}

	/**
	 * Store a newly created Video_scores in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Video_scores::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Video_scores::create($data);

		return Redirect::route('Video_scores.index');
	}

	/**
	 * Display the specified Video_scores.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$Video_scores = Video_scores::findOrFail($id);

		return View::make('Video_scores.show', compact('Video_scores'));
	}

	/**
	 * Show the form for editing the specified Video_scores.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$Video_scores = Video_scores::find($id);

		return View::make('Video_scores.edit', compact('Video_scores'));
	}

	/**
	 * Update the specified Video_scores in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$Video_scores = Video_scores::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Video_scores::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$Video_scores->update($data);

		return Redirect::route('Video_scores.index');
	}

	/**
	 * Remove the specified Video_scores from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Video_scores::destroy($id);

		return Redirect::route('Video_scores.index');
	}

}
