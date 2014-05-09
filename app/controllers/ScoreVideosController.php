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
		foreach($video_scores as $score) {
			$videos[$score->video->name][] = $score;
		}

		//dd(DB::getQueryLog());

		return View::make('video_scores.index', compact('videos'));
	}

	// Choose an appopriate video for judging
	// Display video to be judged
	public function score($video_group)
	{
		Breadcrumbs::addCrumb('Score Video', 'score');

		// Get the first video with the lowest number of
		// scores, not scored by the current user.
		// If scores are present, discount
		$video_list = DB::table('videos')
					 ->leftJoin('video_scores', 'videos.id', '=', 'video_scores.video_id')
					 ->leftJoin('vid_score_types', 'video_scores.vid_score_type_id', '=', 'vid_score_types.id')
					 ->whereNull('video_scores.judge_id')
					 ->orWhere(function($q) use ($video_group) {
					 		$q->where('video_scores.judge_id', '<>', Auth::user()->ID)
					 		  ->where('vid_score_types.group', $video_group);
					 	})
					 ->select(DB::raw('videos.id, COUNT(*) as score_count'))
					 ->orderBy('score_count', 'ASC')
					 ->groupBy('videos.id')
					 ->get();

		$video = Video::find($video_list[0]->id);
		$types = Vid_score_type::where('group', $video_group)->with('Rubric')->get();

		return View::make('video_scores.create', compact('video', 'types'));

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

	// Take an individual raw score from the form and turn it into
	// something which can be used to create or update
	// a score record
	private function calculate_score($type, $score)
	{
		$total = 0;
		$score_count = count($score);
		// Loop through s1..s5, totalling or creating the index
		for($i = 1; $i < 6; $i++)
		{
			$index = 's' . $i;
			if(array_key_exists($index, $score)) {
				$total += $score[$index];
			} else {
				$score[$index] = 0;
			}
		}
		$score['total'] = $total;
		$score['average'] = $total / $score_count;
		$score['norm_avg'] = $score['average'];
		$score['vid_score_type_id'] = $type;

		return $score;
	}

	/**
	 * Store a newly created video_scores.in storage.
	 *
	 * @return Response
	 */
	public function store($video_id)
	{
		$input = Input::all();
		$video = Video::find($video_id);

		foreach($input['scores'] as $type => $score) {
			$score = $this->calculate_score($type, $score);
			$score['video_id'] = $video_id;
			$score['vid_division_id'] = $video->vid_division_id;
			$score['judge_id'] = Auth::user()->ID;
			Video_scores::create($score);
		}

		return Redirect::route('video.judge.index');
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
