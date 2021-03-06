<?php

class FileTypesController extends \BaseController {

	public function __construct()
	{
		parent::__construct();

		Breadcrumbs::addCrumb('Manage Filetypes', 'filetypes');
	}


	/**
	 * Display a listing of filetypes
	 *
	 * @return Response
	 */
	public function index()
	{
		$filetype_list = Filetype::all();
		$cat_list = $filetype_list->lists('name', 'type');

		$filetypes = [];

	    foreach($filetype_list as $filetype) {
	        $filetypes[$cat_list[$filetype->type]]['cat'] = $filetype->type;
	        $filetypes[$cat_list[$filetype->type]]['types'][] = $filetype->toArray();
        }

        uksort($filetypes, function($a, $b) { return strcasecmp($a, $b) ; });

        View::share('title', 'Manage Filetypes');
		return View::make('filetypes.index', compact('filetypes'));
	}

	/**
	 * Show the form for creating a new myfiletype
	 *
	 * @return Response
	 */
	public function create($type = "doc")
	{
	    $filetype_list = Filetype::all();
		$cat_list = $filetype_list->lists('name', 'type');
		$unique_id = rand(1000, 10000);

		return View::make('filetypes.create', compact('type', 'cat_list', 'unique_id'));
	}

	/**
	 * Store a newly created myfiletype in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
	    $filetype_list = Filetype::all();
		$cat_list = $filetype_list->lists('name', 'type');
		$data = Input::all();
		$data['name'] = $cat_list[$data['type']];

		$validator = Validator::make($data, Filetype::$rules);

		if ($validator->fails())
		{
			return View::make('filetypes.create')->withErrors($validator)->withInput();
		}

		$type = Filetype::create($data)->toArray();

		return View::make('filetypes.partial.typerow', compact('type'));
	}

	/**
	 * Display the specified myfiletype.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$type = Filetype::findOrFail($id)->toArray();

		return View::make('filetypes.partial.typerow', compact('type'));
	}

	/**
	 * Show the form for editing the specified myfiletype.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$type = Filetype::findOrFail($id);
		$filetype_list = Filetype::all();
		$cat_list = $filetype_list->lists('name', 'type');

		return View::make('filetypes.edit', compact('type','cat_list'));
	}

	/**
	 * Update the specified myfiletype in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$type = Filetype::findOrFail($id);

        $filetype_list = Filetype::all();
		$cat_list = $filetype_list->lists('name', 'type');

		$data = Input::all();
		$data['name'] = $cat_list[$data['type']];

		$validator = Validator::make($data, Filetype::$rules);

		if ($validator->fails())
		{
			return View::make('filetypes.edit')->with(compact('type','data'))->withErrors($validator)->withInput();
		}

		$type->update($data);

		return View::make('filetypes.partial.typerow', compact('type'));
	}

	/**
	 * Remove the specified myfiletype from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Filetype::destroy($id);

		return Redirect::route('filetypes.index');
	}

}
