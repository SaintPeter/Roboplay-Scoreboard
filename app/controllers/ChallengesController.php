<?php

class ChallengesController extends BaseController {

	/**
	 * Challenge Repository
	 *
	 * @var Challenge
	 */
	protected $challenge;

	public function __construct(Challenge $challenge)
	{
		parent::__construct();
		$this->challenge = $challenge;
		Breadcrumbs::addCrumb('Manage Challenges', route('challenges.index'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Input::has('selected_year')) {
			$selected_year = Input::get('selected_year');
			if($selected_year == 'clear') {
				Session::forget('selected_year');
				$selected_year = false;
			} else {
				Session::put('selected_year', $selected_year);
			}
		} else {
			$selected_year = Session::get('selected_year', false);
		}

		if(Input::has('level_select')) {
			$level_select = Input::get('level_select');
			if($level_select == 0) {
				Session::forget('level_select');
				$level_select = false;
			} else {
				Session::put('level_select', $level_select);
			}
		} else {
			$level_select = Session::get('level_select', false);
		}

		$challenge_query = Challenge::with('score_elements');

		if($level_select) {
			$challenge_query = $challenge_query->where('level', $level_select);
		}

		if($selected_year) {
			$challenge_query = $challenge_query->where('year', $selected_year);
		}

		$challenges = $challenge_query->get();

		View::share('title', 'Manage Challenges');
		return View::make('challenges.index', compact('challenges'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		Breadcrumbs::addCrumb('Add Challenge', route('challenges.create'));
		View::share('title', 'Add Challenge');
		return View::make('challenges.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$input['year'] = Carbon\Carbon::now()->year;
		$validation = Validator::make($input, Challenge::$rules);

		if ($validation->passes())
		{
			$challenge = $this->challenge->create($input);

			return Redirect::route('challenges.show', [ $challenge->id ]);
		}

		return Redirect::route('challenges.create')
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
		Breadcrumbs::addCrumb('Show Challenge', route('challenges.show', $id));
		View::share('title', 'Show Challenge');
		$challenge = $this->challenge->with('score_elements')->findOrFail($id);

		return View::make('challenges.show', compact('challenge'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		Breadcrumbs::addCrumb('Edit Challenge', route('challenges.edit', $id));
		View::share('title', 'Edit Challenge');
		$challenge = $this->challenge->find($id);

		if (is_null($challenge))
		{
			return Redirect::route('challenges.index');
		}

		return View::make('challenges.edit', compact('challenge'));
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
		$validation = Validator::make($input, Challenge::$rules);

		if ($validation->passes())
		{
			$challenge = $this->challenge->find($id);
			$challenge->update($input);

			return Redirect::route('challenges.show', $id);
		}

		return Redirect::route('challenges.edit', $id)
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
		$this->challenge->find($id)->delete();

		return Redirect::route('challenges.index');
	}

}
