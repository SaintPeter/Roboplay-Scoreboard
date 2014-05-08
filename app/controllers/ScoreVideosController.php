<?PHP

define('VG_GENERAL', 1);
define('VG_PART', 2);
define('VG_COMPUTE', 3);

class ScoreVideosController extends \BaseController {

	public function __construct()
	{
		parent::__construct();

		Breadcrumbs::addCrumb('Judge Videos', 'video/judge');
	}

	/**
	 * Display a listing of Video_scores
	 *
	 * @return Response
	 */
	public function index()
	{
		$video_scores = Video_scores::where('judge_id', Auth::user()->ID)
								  ->orderBy('total', 'desc')
								  ->get();

		return View::make('video_scores.index', compact('video_scores'));
	}

	// Choose an appopriate video for judging
	// Display video to be judged
	public function score($video_group)
	{
		Breadcrumbs::addCrumb('Score Video', 'score');

	}

	/**
	 * Show the form for creating a new Video_scores
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('video_scores.create');
	}

	/**
	 * Store a newly created video_scores.in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Video_scores::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Video_scores::create($data);

		return Redirect::route('video_scores.index');
	}

	/**
	 * Display the specified video_scores.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($video_id)
	{
		Breadcrumbs::addCrumb('View Scores', 'score');
		//$video_scores.= Video_scores::findOrFail($id);

		return View::make('video_scores.show', compact('video_scores'));
	}

	/**
	 * Show the form for editing the specified video_scores.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		Breadcrumbs::addCrumb('Edit Scores', 'edit');
		$video_scores.= Video_scores::find($id);

		return View::make('video_scores.edit', compact('video_scores'));
	}

	/**
	 * Update the specified video_scores.in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$video_scores.= Video_scores::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Video_scores::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$video_scores->update($data);

		return Redirect::route('video_scores.index');
	}

	/**
	 * Remove the specified video_scores.from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Video_scores::destroy($id);

		return Redirect::route('video_scores.index');
	}

}
