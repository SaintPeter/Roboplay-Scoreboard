<?PHP

define('VG_GENERAL', 1);
define('VG_PART', 2);
define('VG_COMPUTE', 3);

class ScoreVideosController extends \BaseController {
	public $group_names = [ VG_GENERAL => "General",
							VG_PART    => "Custom Part",
							VG_COMPUTE => "Computational Thinking" ];

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
		// Get a list of active Video Competitions
		$competiton = Vid_competition::with('divisions')
								->where('event_start', '<=', date('Y-m-d'))
								->where('event_end', '>=', date('Y-m-d'))
								->get();

		$comp_list = [];
		$div_list = [];
		foreach($competiton as $comp) {
			foreach($comp->divisions as $div) {
				$comp_list[$comp->name][] = $div->name;
				$div_list[] = $div->id;
			}
		}

		// Get a list of all videos this judge has scored
		$video_scores = Video_scores::with('division', 'division.competition')
							->where('judge_id', Auth::user()->ID)
							->orderBy('total', 'desc')
							->get();
		$videos = [];
		$types = Vid_score_type::orderBy('id')->lists('name', 'id');

		$blank = array_combine(array_keys($types), array_fill(0, count($types), '-'));
		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name] = $blank;
		}

		$scored_count = array_combine(array_keys($this->group_names), array_fill(0, count($this->group_names), 0));
		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name][$score->vid_score_type_id] = $score;
			$scored_count[$score->score_group]++;
		}

		// Fix category count for VG_GENERAL
		$scored_count[VG_GENERAL] /= 3;

		$total_count[VG_GENERAL] = Video::whereIn('vid_division_id', $div_list)->count();
		$total_count[VG_PART] = Video::whereIn('vid_division_id', $div_list)->where('has_custom', true)->count();
		$total_count[VG_COMPUTE] = Video::whereIn('vid_division_id', $div_list)->where('has_code', true)->count();

		//dd($total_count);

		View::share('title', 'Judge Videos');
		return View::make('video_scores.index', compact('videos', 'comp_list', 'types', 'total_count', 'scored_count'));
	}

	// Choose an appopriate video for judging
	// Display video to be judged
	public function dispatch($video_group)
	{
		// Get a list of active Video Competitions
		$comps = Vid_competition::with('divisions')
								->where('event_start', '<=', date('Y-m-d'))
								->where('event_end', '>=', date('Y-m-d'))
								->get();
		$divs = [0];
		foreach($comps as $comp) {
			$divs = array_merge($divs, $comp->divisions->lists('id'));
		}

		// Get all the videos and any comments for this score group
		$video_query = Video::with([ 'scores' => function($q) use ($video_group) {
							$q->where('video_scores.score_group', $video_group);
						}])
						->whereIn('vid_division_id', $divs);
		if($video_group == VG_PART) {
			$all_videos = $video_query->where('has_custom', 1)->get();
		} elseif ($video_group == VG_COMPUTE) {
			$all_videos = $video_query->where('has_code', 1)->get();
		} else {
		 	$all_videos = $video_query->get();
		}
		//dd(DB::getQueryLog());
//		echo "<pre>";
//		foreach($all_videos as $video) {
//			echo $video->id . " - scores: " . count($video->scores) . "<br />";
//		}
//		echo "</pre>";

		$filtered = $all_videos->filter(function($video) {
			if(count($video->scores) == 0) {
				// Videos with no scores stay on the list
				return true;
			} else {
				foreach($video->scores as $score) {
					if($score->judge_id == Auth::user()->ID) {
						return false;
					}
				}
				return true;
			}
		});

//		echo "<pre>";
//		foreach($filtered as $video) {
//			echo $video->id . " - scores: " . count($video->scores) . "<br />";
//		}
//		echo "</pre>";

		if(count($filtered) == 0) {
			return Redirect::route('video.judge.index')->with('message', 'You cannot judge any more of this type of video.');
		}

		$sorted = $filtered->sort( function ($a, $b) {
				$count_a = count($a->scores);
				$count_b = count($b->scores);
			    if ($count_a == $count_b) {
			        return 0;
			    }
			    return ($count_a < $count_b) ? -1 : 1;
			});


		//$video = Video::find($video_list[0]->id);
		$video = $sorted->first();
		//$types = Vid_score_type::where('group', $video_group)->with('Rubric')->get();

		//return View::make('video_scores.create', compact('video', 'types'));
		return Redirect::route('video.judge.score', [ 'video_id' => $video->id, 'video_group' => $video_group ] );

	}

	// Score a Specific Video/Video Group combination
	public function score($video_id, $video_group) {
		Breadcrumbs::addCrumb('Score Video', 'score');
		View::share('title', 'Score Video');

		$video = Video::find($video_id);
		if(empty($video)) {
			// Invalid video
			return Redirect::route('video.judge.index')
							->with('message', "Invalid video id '$video_id'.  Video no longer exists or another error occured.");
		}

		$score_count = Video_scores::where('video_id', $video_id)
								   ->where('score_group', $video_group)
								   ->where('judge_id', Auth::user()->ID)
								   ->count();
		if($score_count > 0) {
			return Redirect::route('video.judge.edit', [ 'video_id' => $video_id ])
							->with('message', 'You already scored this video.  Switched to Edit Mode.');
		}

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
		$group = Vid_score_type::whereId($type)->pluck('group');
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
		$score['score_group'] = $group;

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
		Breadcrumbs::addCrumb('Show Video', 'score');
		View::share('title', 'Show Video');

		$video = Video::find($video_id);
		if(empty($video)) {
			// Invalid video
			return Redirect::route('video.judge.index')
							->with('message', "Invalid video id '$video_id'.  Video no longer exists or another error occured.");
		}

		return View::make('video_scores.show', compact('video'));
	}

	/**
	 * Show the form for editing the specified video_scores.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($video_id)
	{
		Breadcrumbs::addCrumb('Edit Score', 'edit');
		View::share('title', 'Edit Video Score');
		$video = Video::find($video_id);
		if(empty($video)) {
			// Invalid video
			return Redirect::route('video.judge.index')
							->with('message', "Invalid video id '$video_id'.  Video no longer exists or another error occured.");
		}

		$scores = Video_scores::where('video_id', $video_id)
								   ->where('judge_id', Auth::user()->ID)
								   ->get();
		if(count($scores)==0) {
			return Redirect::route('video.judge.index')
							->with('message', 'No scores to edit for this video.');
		}
		//dd($scores);

		$groups = [ ];
		$groups = array_keys($scores->lists('score_group', 'score_group'));

		//$missing_groups = array_diff([ VG_COMPUTE, VG_GENERAL, VG_PART ], $groups);

		$types = Vid_score_type::whereIn('group', $groups)->with('Rubric')->get();
		//dd($types);

		$video_scores = [];
		foreach($scores as $score) {
			$video_scores[$score->vid_score_type_id]['id'] = $score->id;
			$video_scores[$score->vid_score_type_id]['s1'] = $score->s1;
			$video_scores[$score->vid_score_type_id]['s2'] = $score->s2;
			$video_scores[$score->vid_score_type_id]['s3'] = $score->s3;
			$video_scores[$score->vid_score_type_id]['s4'] = $score->s4;
			$video_scores[$score->vid_score_type_id]['s5'] = $score->s5;
		}

		return View::make('video_scores.edit', compact('video', 'video_scores', 'types'))
						->with('group_names', $this->group_names);
	}

	/**
	 * Update the specified video_scores.in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($video_id)
	{
		$input = Input::all();
		$video = Video::find($video_id);

		foreach($input['scores'] as $type => $score) {
			$score = $this->calculate_score($type, $score);
			$score['video_id'] = $video_id;
			$score['vid_division_id'] = $video->vid_division_id;
			$score['judge_id'] = Auth::user()->ID;
			$this_score = Video_scores::find($score['id']);
			$this_score->update($score);
		}

		return Redirect::route('video.judge.index');
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
