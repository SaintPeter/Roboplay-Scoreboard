<?php

class DivisionsController extends BaseController {

	/**
	 * Division Repository
	 *
	 * @var Division
	 */
	protected $division;

	public function __construct(Division $division)
	{
		parent::__construct();
		$this->division = $division;
		Breadcrumbs::addCrumb('Competition Divisions', route('divisions.index'));
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$divisions = $this->division->with('competition', 'challenges')
						  ->orderBy('competition_id')
						  ->orderBy('display_order')
						  ->get();
		View::share('title', 'Competition Divisions');
		return View::make('divisions.index', compact('divisions'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		Breadcrumbs::addCrumb('Add Division', route('divisions.create'));
		View::share('title', 'Add Competition Division');

		$competitions = Competition::lists('name','id');

		return View::make('divisions.create')
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
		$validation = Validator::make($input, Division::$rules);

		if ($validation->passes())
		{
			$this->division->create($input);

			return Redirect::route('divisions.index');
		}

		return Redirect::route('divisions.create')
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
		Breadcrumbs::addCrumb('Show Division', route('divisions.show', $id));
		View::share('title', 'Show Division');
		$division = $this->division->with('competition','challenges','challenges.score_elements')->findOrFail($id);
		$challenges = $division->challenges;

		return View::make('divisions.show', compact('division', 'challenges'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		Breadcrumbs::addCrumb('Edit Division', route('divisions.edit', $id));
		View::share('title', 'Edit Division');
		$division = $this->division->find($id);

		if (is_null($division))
		{
			return Redirect::route('divisions.index');
		}

		$competitions = Competition::lists('name','id');

		return View::make('divisions.edit', compact('division'))
				   ->with('competitions', $competitions);
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
		$validation = Validator::make($input, Division::$rules);

		if ($validation->passes())
		{
			$division = $this->division->find($id);
			$division->update($input);

			return Redirect::route('divisions.index');
		}

		return Redirect::route('divisions.edit', $id)
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
		$this->division->find($id)->delete();

		return Redirect::route('divisions.index');
	}

	/**
	 * List all challenges and assign them to the given division
	 *
	 * @param  int  $division_id
	 * @return Response
	 */
	public function assign($division_id)
	{
		Breadcrumbs::addCrumb('Show Division', route('divisions.show', $division_id));
		Breadcrumbs::addCrumb('Assign Challenges', '');
		View::share('title', 'Assign Challenges');
	 	$all_challenges = Challenge::with('divisions')->get();

		$all_list = array();
		$selected_list = array();

		$all_list = $all_challenges->lists('internal_name', 'id');


	 	foreach($all_challenges as $challenge) {
	 		if($challenge->divisions->contains($division_id)) {
	 			$selected_list[] =  $challenge->id;
	 		}
	 	}

	 	return View::make('divisions.assign', compact('all_list', 'selected_list', 'division_id'));
	}

	public function saveassign()
	{
		$has_list = Input::get('has', array());
		$division_id = Input::get('division_id', 0);

		$division = Division::with('challenges')->find($division_id);

		$division->challenges()->sync($has_list);

		return Redirect::route('divisions.show', $division_id);
	}

	public function removeChallenge($division_id, $challenge_id)
	{
		$division = Division::with('challenges')->findOrFail($division_id);

		$division->challenges()->detach($challenge_id);

		return Redirect::route('divisions.show', $division_id);
	}

	// Reorder the challenges to match those passed in via POST
	public function updateChallengeOrder($division_id)
	{
		$challenge_list = Input::get('challenge', array());
		$division = Division::with('challenges', 'challenges.score_elements')->findOrFail($division_id);

		$order = 1;
		foreach($challenge_list as $challenge_id) {
			$update[$challenge_id] = array('display_order' => $order);
			$order++;
		}

		$division->challenges()->sync($update);

		$division = Division::with('challenges', 'challenges.score_elements')->findOrFail($division_id);

		return View::make('divisions.partial.challenges')
					->with('challenges', $division->challenges)
					->with(compact('division'));

	}

}
