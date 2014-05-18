<?php

class CompetitionsController extends BaseController {

	/**
	 * Competition Repository
	 *
	 * @var Competition
	 */
	protected $competition;

	public function __construct(Competition $competition)
	{
		parent::__construct();
		$this->competition = $competition;
		Breadcrumbs::addCrumb('Manage Competitions', 'competitions');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$competitions = $this->competition->all();

		View::share('title', 'Manage Competitions');
		return View::make('competitions.index', compact('competitions'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		View::share('title', 'Add Competition');
		Breadcrumbs::addCrumb('Add Competition', 'create');
		return View::make('competitions.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Competition::$rules);

		if ($validation->passes())
		{
			$this->competition->create($input);

			return Redirect::route('competitions.index');
		}

		return Redirect::route('competitions.create')
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
		$competition = $this->competition->findOrFail($id);

		View::share('title', 'Show Competition');
		return View::make('competitions.show', compact('competition'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		Breadcrumbs::addCrumb('Edit Competition', 'edit');
		View::share('title', 'Edit Competition');
		$competition = $this->competition->find($id);

		if (is_null($competition))
		{
			return Redirect::route('competitions.index');
		}

		return View::make('competitions.edit', compact('competition'));
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
		$validation = Validator::make($input, Competition::$rules);

		if ($validation->passes())
		{
			$competition = $this->competition->find($id);
			$competition->update($input);

			return Redirect::route('competitions.show', $id);
		}

		return Redirect::route('competitions.edit', $id)
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
		$this->competition->find($id)->delete();

		return Redirect::route('competitions.index');
	}

}
