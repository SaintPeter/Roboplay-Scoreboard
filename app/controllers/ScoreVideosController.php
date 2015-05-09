<?PHP

class ScoreVideosController extends \BaseController {
	public $group_names = [ VG_GENERAL => "General",
							VG_CUSTOM    => "Custom Part",
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
		$date = Carbon\Carbon::now()->setTimezone('America/Los_Angeles')->toDateString();
		// Get a list of active Video Competitions
		$competiton = Vid_competition::with('divisions')
								->where('event_start', '<=', $date)
								->where('event_end', '>=', $date)
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
		if(!empty($div_list)) {
			$video_scores = Video_scores::with('division', 'division.competition', 'video', 'video.comments')
								->where('judge_id', Auth::user()->ID)
								->whereIn('vid_division_id', $div_list)
								->orderBy('total', 'desc')
								->get();
		} else {
			$video_scores = [];
		}
		$videos = [];
		$types = Vid_score_type::orderBy('id')->lists('name', 'id');

		// Create blank list of scores
		$blank = array_combine(array_keys($types), array_fill(0, count($types), '-'));
		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name] = $blank;
		}

		// Populate score list with actual scores
		$scored_count = array_combine(array_keys($this->group_names), array_fill(0, count($this->group_names), 0));
		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name][$score->vid_score_type_id] = $score;
			$videos[$score->division->longname()][$score->video->name]['video_id'] = $score->video_id;
			$videos[$score->division->longname()][$score->video->name]['flag'] = $score->video->flag;
			$scored_count[$score->score_group]++;
		}

		// Fix category count for VG_GENERAL
		$scored_count[VG_GENERAL] /= 3;

		if(count($div_list) > 0) {
			$total_count[VG_GENERAL] = Video::whereIn('vid_division_id', $div_list)->count();
			$total_count[VG_CUSTOM] = Video::whereIn('vid_division_id', $div_list)->where('has_custom', true)->count();
			$total_count[VG_COMPUTE] = Video::whereIn('vid_division_id', $div_list)->where('has_code', true)->count();
		} else {
			$total_count[VG_GENERAL] = 0;
			$total_count[VG_CUSTOM] = 0;
			$total_count[VG_COMPUTE] = 0;
		}

		// Setup toggle boxes based on cookie
		$judge_compute = Cookie::get('judge_compute',0) ? 'checked="checked"' : '';
		$judge_custom = Cookie::get('judge_custom',0) ? 'checked="checked"' : '';

		//dd($judge_compute, $judge_custom);

		View::share('title', 'Judge Videos');
		return View::make('video_scores.index', compact('videos', 'comp_list', 'types', 'total_count', 'scored_count', 'judge_compute', 'judge_custom'));
	}

	// Choose an appopriate video for judging
	// Display video to be judged
	public function dispatch()
	{
		// Get toggle statuses
		if(Input::has('judge_compute')) {
			Cookie::queue('judge_compute', 1, 5 * 365 * 24 * 60 * 60);
			$judge_compute = true;
		} else {
			Cookie::queue('judge_compute', null, -1);
			$judge_compute = false;
		}

		if(Input::has('judge_custom')) {
			Cookie::queue('judge_custom', 1, 5 * 365 * 24 * 60 * 60);
			$judge_custom = true;
		} else {
			Cookie::queue('judge_custom', null, -1);
			$judge_custom = false;
		}

		// Get a list of active Video Competitions
		$comps = Vid_competition::with('divisions')
								->where('event_start', '<=', date('Y-m-d'))
								->where('event_end', '>=', date('Y-m-d'))
								->get();
		$divs = [0];
		foreach($comps as $comp) {
			$divs = array_merge($divs, $comp->divisions->lists('id'));
		}

		// Get all the videos and their scores were the video is not flagged for review or disqualified
		$all_videos = Video::with('scores')->where('flag',FLAG_NORMAL)->whereIn('vid_division_id', $divs)->get();

		//dd(DB::getQueryLog());
//		echo "<pre>";
//		foreach($all_videos as $video) {
//			echo $video->id . " - scores: " . count($video->scores) . "<br />";
//		}
//		echo "</pre>";

		// Remove videos which have no scores or which this judge has scored before
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

//		echo "Filtered: <br/><pre>";
//		foreach($filtered as $video) {
//			echo $video->id . " - scores: " . count($video->scores) . " Custom: {$video->has_custom} Code: {$video->has_code} - {$video->name}<br />";
//		}
//		echo "</pre>";

		if(count($filtered) == 0) {
			return Redirect::route('video.judge.index')->with('message', 'You cannot judge any more videos.');
		}

		// Sort videos to determine which one gets scored next
		// In this code minus is used as a stand in for the non-existant spaceship operator <=>
		// Logic:
		//   Top priority is custom parts.  Sort Descending.  Ignore if judge doesn't score custom.
		//   Second priority is code.  Sort Descending.  Ignore if judge doesn't score code.
		//   Third Priority is everything else.  Sort by count of scores, ascending.
		$sorted = $filtered->sort( function ($a, $b) use ($judge_compute, $judge_custom){
				// Has Custom?
				$has_custom = $b->has_custom - $a->has_custom;
				if($has_custom == 0 OR !$judge_custom) {
					// Custom is the same, check code
					$has_code = $b->has_code - $a->has_code;
					if($has_code == 0 OR !$judge_compute) {
						// Code is the same, check count
						return count($a->scores) - count($b->scores);
					} else {
						return $has_code;
					}
				} else {
					// Custom Differs
					return $has_custom;
				}
			});

//		echo "Sorted:<br/><pre>";
//		foreach($sorted as $video) {
//			echo $video->id . " - scores: " . count($video->scores) . " Custom: {$video->has_custom} Code: {$video->has_code} - {$video->name}<br />";
//		}
//		echo "</pre>";
//		exit;

		// The top item on the list gets scored next
		$video = $sorted->first();

		//return View::make('video_scores.create', compact('video', 'types'));
		return Redirect::route('video.judge.score', [ 'video_id' => $video->id, 'no-cache' => microtime() ] );

	}

	// Score a Specific Video combination
	public function score($video_id) {
		Breadcrumbs::addCrumb('Score Video', 'score');
		View::share('title', 'Score Video');

		$video = Video::with('vid_division.competition')->find($video_id);
		if(empty($video)) {
			// Invalid video
			return Redirect::route('video.judge.index')
							->with('message', "Invalid video id '$video_id'.  Video no longer exists or another error occured.");
		}
//dd(DB::getQueryLog());
		// We always score general
		$video_types = [ VG_GENERAL ];

		// Judge Custom Parts
		if(Cookie::has('judge_custom')) {
			$video_types[] = VG_CUSTOM;
		}

		// Judge Computational Thinking
		if(Cookie::has('judge_compute')) {
			$video_types[] = VG_COMPUTE;
		}

		// Ensure we have not already scored this video
		$score_count = Video_scores::where('video_id', $video_id)
								   ->whereIn('score_group', $video_types)
								   ->where('judge_id', Auth::user()->ID)
								   ->count();

		if($score_count > 0) {
			return Redirect::route('video.judge.edit', [ 'video_id' => $video_id ])
							->with('message', 'You already scored this video.  Switched to Edit Mode.');
		}

		$vid_competition_id = $video->vid_division->competition->id;

		$types = Vid_score_type::with( [ 'Rubric' => function($q) use ($vid_competition_id) {
			return $q->where('vid_competition_id', $vid_competition_id);
		}])->whereIn('group', $video_types)->get();
		//dd($types);
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
		$input = Input::except('report_problem','comment');
		$video = Video::find($video_id);

		foreach($input['scores'] as $type => $score) {
			$score = $this->calculate_score($type, $score);
			$score['video_id'] = $video_id;
			$score['vid_division_id'] = $video->vid_division_id;
			$score['judge_id'] = Auth::user()->ID;
			Video_scores::create($score);
		}

		// Deal with problem reports
		if(Input::has('report_problem') AND Input::has('comment')) {
			$video_comment['video_id'] = $video->id;
			$video_comment['judge_id'] = Auth::user()->ID;
			$video_comment['comment'] = Input::get('comment', '--No Comment Entered--');

			Video_comment::create($video_comment);

			// Flag the video for Review
			$video->flag = FLAG_REVIEW;
			$video->save();

			// TODO:  E-mail admin to let them know a video has been flagged
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

		//$missing_groups = array_diff([ VG_COMPUTE, VG_GENERAL, VG_CUSTOM ], $groups);

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

		// Deal with problem reports
		if(Input::has('report_problem') AND Input::has('comment')) {
			$video_comment['video_id'] = $video->id;
			$video_comment['judge_id'] = Auth::user()->ID;
			$video_comment['comment'] = Input::get('comment', '--No Comment Entered--');

			Video_comment::create($video_comment);

			// Flag the video for Review
			$video->flag = FLAG_REVIEW;
			$video->save();

			// TODO:  E-mail admin to let them know a video has been flagged
		}

		return Redirect::route('video.judge.index');
	}

	// Clear scores for a specific video and judge
	public function clear_scores($video_id, $judge_id) {
		// Only owners may clear their own scores, or admins
		if(Roles::isAdmin() OR $judge_id == Auth::user()->ID) {
			Video_scores::where('video_id', $video_id)->where('judge_id', $judge_id)->delete();

			return Redirect::route('video.judge.index')->with('message', 'Score Cleared');

		} else {
			return Redirect::route('video.judge.index')->with('message','You do not have permission to clear these scores');
		}

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
