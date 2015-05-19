<?php

class CompYearsController extends \BaseController {
	public function __construct(Challenge $challenge)
	{
		parent::__construct();
		Breadcrumbs::addCrumb('Manage Competition Years', route('compyears.index'));
	}

	/**
	 * Display a listing of compyears
	 *
	 * @return Response
	 */
	public function index()
	{
		$compyears = CompYear::with('competitions', 'divisions', 'vid_competitions', 'vid_divisions')->get();


		View::share('title', 'Manage Competition Years');
		return View::make('compyears.index', compact('compyears'));
	}

	/**
	 * Show the form for creating a new compyear
	 *
	 * @return Response
	 */
	public function create()
	{
		$competition_list = Competition::has('comp_year', 0)->lists('name', 'id');
		$vid_competition_list = Vid_competition::has('comp_year', 0)->lists('name', 'id');

		return View::make('compyears.create')->with(compact('competition_list', 'vid_competition_list'));
	}

	/**
	 * Store a newly created compyear in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::except('_token','competitions','vid_competitions'), CompYear::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$compyear = CompYear::firstOrCreate($data);

		$competition_list = Input::get('competitions', [ 0 ]);
		$vid_competition_list = Input::get('vid_competitions', [ 0 ]);

		$divison_list = [];
		$division_list = Division::whereIn('competition_id', $competition_list)->lists('id');

		$vid_divison_list = [];
		$vid_divison_list = Vid_division::whereIn('competition_id', $vid_competition_list)->lists('id');

		$compyear->competitions()->sync($competition_list);
		$compyear->divisions()->sync($division_list);
		$compyear->vid_competitions()->sync($vid_competition_list);
		$compyear->vid_divisions()->sync($vid_divison_list);

		return Redirect::route('compyears.index');
	}

	/**
	 * Display the specified compyear.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$compyear = CompYear::findOrFail($id);

		return View::make('compyears.show')->with(compact('compyear'));
	}

	/**
	 * Show the form for editing the specified compyear.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$compyear = CompYear::find($id);

		$competition_list = Competition::all()->lists('name', 'id');
		$comp_selected = $compyear->competitions()->lists('yearable_id');
		$vid_competition_list = Vid_competition::all()->lists('name', 'id');
		$vid_selected = $compyear->vid_competitions()->lists('yearable_id');

		return View::make('compyears.edit', compact('compyear','competition_list', 'vid_competition_list', 'comp_selected', 'vid_selected'));
	}

	/**
	 * Update the specified compyear in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$compyear = CompYear::findOrFail($id);

		$validator = Validator::make($data = Input::all(), CompYear::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$compyear->update($data);

		$competition_list = Input::get('competitions', [ 0 ]);
		$vid_competition_list = Input::get('vid_competitions', [ 0 ]);

		$divison_list = [];
		$division_list = Division::whereIn('competition_id', $competition_list)->lists('id');

		$vid_divison_list = [];
		$vid_divison_list = Vid_division::whereIn('competition_id', $vid_competition_list)->lists('id');

		$compyear->competitions()->sync($competition_list);
		$compyear->divisions()->sync($division_list);
		$compyear->vid_competitions()->sync($vid_competition_list);
		$compyear->vid_divisions()->sync($vid_divison_list);

		return Redirect::route('compyears.index');
	}

	/**
	 * Remove the specified compyear from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		CompYear::destroy($id);

		return Redirect::route('compyears.index');
	}

}
