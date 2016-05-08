<?php

class ScheduleController extends \BaseController {

	public function __construct()
	{
		parent::__construct();
		Breadcrumbs::addCrumb('Schedule', 'math_divisions');
	}


	/**
	 * Display a listing of the resource.
	 * GET /schedule
	 *
	 * @return Response
	 */
	public function index()
	{
	    View::share('title', 'Schedule Editor');
	    $schedule = Schedule::orderBy('start')->get();

	    Carbon\Carbon::setToStringFormat('g:i:s a');

		return View::make('schedule.index', compact('schedule'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /schedule/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /schedule
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /schedule/{id}
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
	 * GET /schedule/{id}/edit
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
	 * PUT /schedule/{id}
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
	 * DELETE /schedule/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}