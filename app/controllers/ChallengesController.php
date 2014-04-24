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
		Breadcrumbs::addCrumb('Challenges', route('challenges.index'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$challenges = $this->challenge->with('score_elements')->get();

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
		$validation = Validator::make($input, Challenge::$rules);

		if ($validation->passes())
		{
			$this->challenge->create($input);

			return Redirect::route('challenges.index');
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
		Breadcrumbs::addCrumb('Show', route('challenges.show', $id));
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
		Breadcrumbs::addCrumb('Edit', route('challenges.edit', $id));
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
