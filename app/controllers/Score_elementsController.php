<?php

class Score_elementsController extends BaseController {

	/**
	 * Score_element Repository
	 *
	 * @var Score_element
	 */
	protected $score_element;
	public $input_types = [ 'noyes' => 'No/Yes',
							'yesno' => 'Yes/No',
							'low_slider' => 'Low->High Slider',
							'high_slider' => 'High->Low Slider',
							'score_slider' => 'Score Slider' ];

	public function __construct(Score_element $score_element)
	{
		$this->score_element = $score_element;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$score_elements = $this->score_element->all();

		return View::make('score_elements.index', compact('score_elements'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($challenge_id)
	{
		$challenge = Challenge::with('score_elements')->findOrFail($challenge_id);
		$order = $challenge->score_elements->max('element_number') + 1;

		return View::make('score_elements.create')
				   ->with(compact('challenge_id', 'order'))
				   ->with('input_types', $this->input_types);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$validation = Validator::make($input, Score_element::$rules);

		if ($validation->passes())
		{
			$this->score_element->create($input);
			$this->update_order($this->score_element->challenge_id);

			return "true";
		}


		return Redirect::route('score_elements.create', $input['challenge_id'])
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
		$score_element = $this->score_element->findOrFail($id);

		return View::make('score_elements.show', compact('score_element'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$score_element = $this->score_element->find($id);

		if (is_null($score_element))
		{
			return Redirect::route('score_elements.index');
		}

		return View::make('score_elements.edit', compact('score_element'))
				   ->with('input_types', $this->input_types);
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
		$validation = Validator::make($input, Score_element::$rules);

		if ($validation->passes())
		{
			$score_element = $this->score_element->find($id);
			$score_element->update($input);

			$this->update_order($this->score_element->challenge_id);

			return "true";
		}

		return Redirect::route('score_elements.edit', $id)
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
		$score_element = $this->score_element->find($id);
		$challenge_id = $score_element->challenge_id;
		$score_element->delete();

		return Redirect::route('challenges.show', $challenge_id);
	}

	public function update_order($challenge_id) {
		$elements = Score_element::where('challenge_id', $challenge_id)->orderBy('element_number', 'ASC')->get();

		$index = 1;
		foreach ($elements as $element) {
			$element->element_number = $index;
			$element->save();
			$index++;
		}
	}

}
