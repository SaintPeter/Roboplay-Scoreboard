<?php

class Vid_divisionsController extends BaseController {

	/**
	 * Vid_division Repository
	 *
	 * @var Vid_division
	 */
	protected $vid_division;

	public function __construct(Vid_division $vid_division)
	{
		parent::__construct();
		Breadcrumbs::addCrumb('Video Divisions', 'vid_division');
		$this->vid_division = $vid_division;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$vid_divisions = $this->vid_division->all();

		View::share('title', 'Video Divisions');
		return View::make('vid_divisions.index', compact('vid_divisions'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($year = 0)
	{
		Breadcrumbs::addCrumb('Add Division', 'create');
		View::share('title', 'Add Division');
		$competitions = Vid_competition::lists('name','id');

		return View::make('vidcomp.partial.div_create')
					->with('competitions', $competitions);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Vid_division::$rules);
		$compyear = CompYear::findOrFail($input->year);

		if ($validation->passes())
		{
		    $div = new Vid_division($input);
			$compyear->vid_divisions()->save($div);

			return "true";
		}

		return Redirect::route('vidcomp.partial.div_create')
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
		Breadcrumbs::addCrumb('Show Division', 'create');
		View::share('title', 'Show Division');
		$vid_division = $this->vid_division->findOrFail($id);

		return View::make('vid_divisions.show', compact('vid_division'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		Breadcrumbs::addCrumb('Edit Division', 'create');
		View::share('title', 'Edit Division');
		$vid_division = $this->vid_division->find($id);
		$competitions = Vid_competition::lists('name','id');

		if (is_null($vid_division))
		{
			return Redirect::route('vidcomp.index');
		}

		return View::make('vidcomp.partial.div_edit', compact('vid_division'))
				   ->with('competitions',$competitions);
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
		$validation = Validator::make($input, Vid_division::$rules);

		if ($validation->passes())
		{
			$vid_division = $this->vid_division->find($id);
			$vid_division->update($input);

			return "true"
		}

		return Redirect::route('vidcomp.partial.div_edit', $id)
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
		$this->vid_division->find($id)->delete();

		return Redirect::route('vid_divisions.index');
	}

}
