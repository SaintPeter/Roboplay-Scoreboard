<?php

class Vid_competitionsController extends BaseController {

	/**
	 * Vid_competition Repository
	 *
	 * @var Vid_competition
	 */
	protected $vid_competition;

	public function __construct(Vid_competition $vid_competition)
	{
		parent::__construct();
		Breadcrumbs::addCrumb('Video Competitions', 'vid_competitions');
		$this->vid_competition = $vid_competition;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$vid_competitions = $this->vid_competition->all();

		return View::make('vid_competitions.index', compact('vid_competitions'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		Breadcrumbs::addCrumb('Add Video Competitions', 'create');
		return View::make('vid_competitions.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Vid_competition::$rules);

		if ($validation->passes())
		{
			$this->vid_competition->create($input);

			return Redirect::route('vid_competitions.index');
		}

		return Redirect::route('vid_competitions.create')
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
		Breadcrumbs::addCrumb('Show Video Competition', 'show');
		$vid_competition = $this->vid_competition->findOrFail($id);

		return View::make('vid_competitions.show', compact('vid_competition'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		Breadcrumbs::addCrumb('Edit Video Competition', 'edit');
		$vid_competition = $this->vid_competition->find($id);

		if (is_null($vid_competition))
		{
			return Redirect::route('vid_competitions.index');
		}

		return View::make('vid_competitions.edit', compact('vid_competition'));
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
		$validation = Validator::make($input, Vid_competition::$rules);

		if ($validation->passes())
		{
			$vid_competition = $this->vid_competition->find($id);
			$vid_competition->update($input);

			return Redirect::route('vid_competitions.show', $id);
		}

		return Redirect::route('vid_competitions.edit', $id)
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
		$this->vid_competition->find($id)->delete();

		return Redirect::route('vid_competitions.index');
	}

}
