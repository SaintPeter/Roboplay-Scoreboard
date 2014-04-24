<?php

class JudgesController extends BaseController {

	/**
	 * Judge Repository
	 *
	 * @var Judge
	 */
	protected $judge;

	public function __construct(Judge $judge)
	{
		$this->judge = $judge;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$judges = $this->judge->all();

		return View::make('judges.index', compact('judges'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('judges.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Judge::$rules);

		if ($validation->passes())
		{
			$this->judge->create($input);

			return Redirect::route('judges.index');
		}

		return Redirect::route('judges.create')
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
		$judge = $this->judge->findOrFail($id);

		return View::make('judges.show', compact('judge'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$judge = $this->judge->find($id);

		if (is_null($judge))
		{
			return Redirect::route('judges.index');
		}

		return View::make('judges.edit', compact('judge'));
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
		$validation = Validator::make($input, Judge::$rules);

		if ($validation->passes())
		{
			$judge = $this->judge->find($id);
			$judge->update($input);

			return Redirect::route('judges.show', $id);
		}

		return Redirect::route('judges.edit', $id)
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
		$this->judge->find($id)->delete();

		return Redirect::route('judges.index');
	}

}
