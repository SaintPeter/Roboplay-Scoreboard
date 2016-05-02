<?php

class RandomListsController extends \BaseController {

	/**
	 * Display a listing of RandomLists
	 *
	 * @return Response
	 */
	public function index()
	{
		$RandomLists = RandomList::all();

		return View::make('RandomLists.index', compact('RandomLists'));
	}

	/**
	 * Show the form for creating a new RandomList
	 *
	 * @return Response
	 */
	public function create($challenge_id)
	{
		$challenge = Challenge::with('random_lists')->findOrFail($challenge_id);
		$order = $challenge->random_lists->max('display_order') + 1;

		return View::make('random_lists.create')
				   ->with(compact('challenge_id', 'order'));
	}

	/**
	 * Store a newly created RandomList in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), RandomList::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		RandomList::create($data);

		return 'true';
	}

	/**
	 * Display the specified RandomList.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$RandomList = RandomList::findOrFail($id);

		return View::make('RandomLists.show', compact('RandomList'));
	}

	/**
	 * Show the form for editing the specified RandomList.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$random_list = RandomList::find($id);

		return View::make('random_lists.edit', compact('random_list'));
	}

	/**
	 * Update the specified RandomList in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$RandomList = RandomList::findOrFail($id);

		$validator = Validator::make($data = Input::all(), RandomList::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$RandomList->update($data);

		return 'true';
	}

	/**
	 * Remove the specified RandomList from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		RandomList::destroy($id);

		return Redirect::back();
	}

    public function edit_list_elements($random_list_id) {
        $random_list = RandomList::with('elements')->findOrFail($random_list_id);

        $elements = $random_list->get_elements();

        return View::make('random_lists.partials.edit_elements')
                   ->with(compact('elements', 'random_list_id'));
    }


    public function save_list_elements($random_list_id) {
        $names = [ 'd1', 'd2', 'd3', 'd4', 'd5' ];
        $random_list = RandomList::findOrFail($random_list_id);
        RandomListElement::whereIn('id', $random_list->elements->lists('id'))->delete();

        $element_lines = preg_split('/\s*\n\s*/', Input::get('elements'));
        $save = [];
        foreach($element_lines as $elements_raw) {
            $elements = preg_split('/\s*(;|\t)\s*/', $elements_raw);
            for($i = 0; $i < count($elements) AND $i < 5; $i++) {
                if(!empty($elements[$i])) {
                    $save[$names[$i]] = $elements[$i];
                } else {
                    break;
                }
            }
            if(!empty($save)) {
                $save['random_list_id'] = $random_list_id;
                $data[] = $save;
            }
        }
        //ddd($data);
        RandomListElement::insert($data);

        return  Redirect::route('list_elements.show', $random_list_id);
    }

    public function show_list_elements($random_list_id) {
        $random_list = RandomList::with('elements')->findOrFail($random_list_id);

        $elements_list = $random_list->elements;

        //ddd($elements_list);

        return View::make('random_lists.partials.show_elements')->with(compact('elements_list'));
    }

}
