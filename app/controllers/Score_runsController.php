<?php

class Score_runsController extends BaseController {

	/**
	 * Score_run Repository
	 *
	 * @var Score_run
	 */
	protected $score_run;

	public function __construct(Score_run $score_run)
	{
		$this->score_run = $score_run;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$score_runs = $this->score_run->all();

		return View::make('score_runs.index', compact('score_runs'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('score_runs.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Score_run::$rules);

		if ($validation->passes())
		{
			$this->score_run->create($input);

			return Redirect::route('score_runs.index');
		}

		return Redirect::route('score_runs.create')
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
		$score_run = $this->score_run->findOrFail($id);

		return View::make('score_runs.show', compact('score_run'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$score_run = $this->score_run->find($id);

		if (is_null($score_run))
		{
			return Redirect::route('score_runs.index');
		}

		return View::make('score_runs.edit', compact('score_run'));
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
		$validation = Validator::make($input, Score_run::$rules);

		if ($validation->passes())
		{
			$score_run = $this->score_run->find($id);
			$score_run->update($input);

			return Redirect::route('score_runs.show', $id);
		}

		return Redirect::route('score_runs.edit', $id)
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
		$this->score_run->find($id)->delete();

		return Redirect::route('score_runs.index');
	}

}
