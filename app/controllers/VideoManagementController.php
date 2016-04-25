<?php
use JpGraph\JpGraph;
use \Carbon\Carbon;

class VideoManagementController extends \BaseController {

	public function index($year = null)
	{
		Breadcrumbs::addCrumb('Manage Scores');

		$year = is_null($year) ? Session::get('year', false) : intval($year);

		$scores_query = Video_scores::with('division', 'division.competition', 'judge', 'video')
							->orderBy('total', 'desc');
		if($year) {
			$scores_query = $scores_query->where(DB::raw("year(created_at)"), $year);
		}
		$video_scores = $scores_query->get();

		$videos = [];

		$types = Vid_score_type::orderBy('id')->lists('name', 'id');
		$blank = array_combine(array_keys($types), array_fill(0, count($types), '-'));
		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->judge->display_name][$score->video->name] = $blank;
			$videos[$score->division->longname()][$score->judge->display_name][$score->video->name]['video_id'] = $score->video_id;
			$videos[$score->division->longname()][$score->judge->display_name][$score->video->name]['judge_id'] = $score->judge_id;
		}

		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->judge->display_name][$score->video->name][$score->vid_score_type_id] = $score->total;
		}
		//dd(DB::getQueryLog());
		View::share('title', 'Manage Scores');
		return View::make('video_scores.manage.index', compact('videos','types','year'));

	}

	public function summary($year = null)
	{
		Breadcrumbs::addCrumb('Scoring Summary');
		View::share('title', 'Scoring Summary');

		$year = is_null($year) ? Session::get('year', false) : intval($year);

		// Videos with score count
		if($year) {
			$videos = Video::with('scores')->where(DB::raw("year(created_at)"), $year)->get();
		} else {
			$videos = Video::with('scores')->get();
		}

		foreach($videos as $video) {
			$output[$video->vid_division->competition->name][$video->vid_division->name][] = $video;
		}

		return View::make('video_scores.manage.summary', compact('output','year'));
	}

	// Display information about individual judges
	// as well as overall summary info
	public function judge_performance($year = null)
	{
		Breadcrumbs::addCrumb('Judge Performace');
		View::share('title', 'Judge Performance');

		$year = intval($year) OR Session::get('year', false);

		// Judges Scoring Count
		$judge_list = Judge::with( [ 'video_scores' => function($q) use ($year) {
				if($year) {
					return $q->where(DB::raw("year(created_at)"), $year);
				} else {
					return $q;
				}
			} ] )->where('is_judge', true)->get();

		//dd(DB::getQueryLog());

		$judge_score_count = [];
		foreach($judge_list as $judge) {
			$judge_score_count[$judge->display_name] = [ 1 => 0, 2 => 0, 3 => 0, 'total' => 0 ];
			if(count($judge->video_scores)) {
				$judge_score_count[$judge->display_name][1] = $judge->video_scores->reduce(function($count, $score) { return ($score->score_group == 1) ? $count + 1 : $count; }, 0) / 3;
				$judge_score_count[$judge->display_name][2] = $judge->video_scores->reduce(function($count, $score) { return ($score->score_group == 2) ? $count + 1 : $count; }, 0);
				$judge_score_count[$judge->display_name][3] = $judge->video_scores->reduce(function($count, $score) { return ($score->score_group == 3) ? $count + 1 : $count; }, 0);
				$judge_score_count[$judge->display_name]['total'] = array_sum($judge_score_count[$judge->display_name]);
			}
		}

		uasort($judge_score_count, function($a, $b) {
				return $b['total'] - $a['total'];
		});

		return View::make('video_scores.manage.judge_performance', compact('judge_score_count', 'year'));
	}

	// Process the deletion of scores
	// select[judge_id] = [ video_id1, video_id2, . . . ]
	// types = Score Group(s) = 1/2/3/all
	public function process()
	{
		$select = Input::get('select');
		$types = Input::get('types');

		switch($types) {
			case 1:
				$groups = [ 1 ];
				break;
			case 2:
				$groups = [ 2 ];
				break;
			case 3:
				$groups = [ 3 ];
				break;
			case 'all':
				$groups = [ 1, 2, 3 ];
				break;
			default:
				return Redirect::to(URL::previous())->with('message', 'No Score Type Selected');
		}

		$affectedRows = 0;
		foreach($select as $judge_id => $video_list) {
			$affectedRows += Video_scores::where('judge_id', $judge_id)
										 ->whereIn('video_id', $video_list)
										 ->whereIn('score_group', $groups)
										 ->delete();
		}

		return Redirect::to(URL::previous())
					    ->with('message', "Deleted $affectedRows scores");
	}

	// Displays score summary sorted by video then by judge
	public function by_video($year = null)
	{
		Breadcrumbs::addCrumb('Scores By Video');
		$video_scores = Video_scores::with('division', 'division.competition', 'judge', 'video')
							->orderBy('total', 'desc');
		if($year) {
			$video_scores = $video_scores->where(DB::raw("year(created_at)"), $year)->get();
		} else {
			$video_scores = $video_scores->get();
		}

		$videos = [];
		$types = Vid_score_type::orderBy('id')->lists('name', 'id');
		$blank = array_combine(array_keys($types), array_fill(0, count($types), '-'));
		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name][$score->judge->display_name] = $blank;
			$videos[$score->division->longname()][$score->video->name][$score->judge->display_name]['video_id'] = $score->video_id;
			$videos[$score->division->longname()][$score->video->name][$score->judge->display_name]['judge_id'] = $score->judge_id;
		}

		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name][$score->judge->display_name][$score->vid_score_type_id] = $score->total;
		}
		//dd(DB::getQueryLog());
		View::share('title', 'Manage Scores');
		return View::make('video_scores.manage.by_video', compact('videos', 'types', 'year'));

	}

	public function scores_csv($year = null) {
		$video_scores = Video_scores::with('division', 'division.competition', 'judge')
							->orderBy('total', 'desc');
		if($year) {
			$video_scores = $video_scores->where(DB::raw("year(created_at)"), intval($year))->get();
		} else {
			$video_scores = $video_scores->get();
		}

		$videos = [];
		//dd(DB::getQueryLog());
		$types = Vid_score_type::orderBy('id')->lists('name', 'id');
		$blank = array_combine(array_keys($types), array_fill(0, count($types), ''));
		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name][$score->judge->display_name] = $blank;
			$videos[$score->division->longname()][$score->video->name][$score->judge->display_name]['video_id'] = $score->video_id;
			$videos[$score->division->longname()][$score->video->name][$score->judge->display_name]['judge_id'] = $score->judge_id;
		}

		foreach($video_scores as $score) {
			$videos[$score->division->longname()][$score->video->name][$score->judge->display_name][$score->vid_score_type_id] = $score->total;
		}

		$content = 'Division,Video Name,ID,Judge Name,' . join(',', $types) . "\n";

		$line = [];
		foreach($videos as $video_division => $video_list) {
			foreach($video_list as $video_name => $judge_list) {
				foreach($judge_list as $judge_name => $scores) {
					$line[] = $video_division;
					$line[] = $video_name;
					$line[] = $scores['video_id'];
					$line[] = $judge_name;
					foreach($types as $index => $type) {
						$line[] = $scores[$index];
					}
					$content .= '"' . join('","', $line) . "\"\n";
					$line = [];
				}
			}
		}

		//dd($content);

		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="video_scores.csv'
		));
	}

	public function reported_videos($year = null) {
		Breadcrumbs::addCrumb('Reported Videos');
		View::share('title', 'Reported Videos');
		$comments_reported = Video_comment::whereHas('video', function($q) use ($year) {
					if($year) {
						$q = $q->where('year', $year);
					}
					return $q->where('flag', FLAG_REVIEW);
				} )->with('video')->get();

		$comments_resolved = Video_comment::whereHas('video', function($q) use ($year) {
					if($year) {
						$q = $q->where('year', $year);
					}
					return $q->where('flag', '<>', FLAG_REVIEW);
				} )->with('video')->get();

//		$videos_dq = Video::has('comments')->where('flag', FLAG_DISQUALIFIED)->get();
//		$videos_resolved = Video::has('comments')->where('flag', FLAG_NORMAL)->get();

		//dd(DB::getQueryLog());

		return View::make('video_scores.manage.reported_videos', compact('comments_reported', 'comments_resolved','year'));
	}

	public function process_report() {
		if(Input::has('absolve')) {
			$comment = Video_comment::with('video')->find(Input::get('absolve'));
			$comment->resolution = Input::get('resolution', 'No Resolution Given');
			$comment->save();
			$comment->video->flag = FLAG_NORMAL;
			$comment->video->save();
		} elseif (Input::has('dq')) {
			$comment = Video_comment::with('video')->find(Input::get('dq'));
			$comment->resolution = Input::get('resolution', 'No Resolution Given');
			$comment->save();
			$comment->video->flag = FLAG_DISQUALIFIED;
			$comment->video->save();
		}
		return Redirect::to(URL::previous());
	}

	public function unresolve($comment_id) {
		$comment = Video_comment::with('video')->find($comment_id);
		$comment->video->flag = FLAG_REVIEW;
		$comment->video->save();
		return Redirect::to(URL::previous());
	}

	public function graph_video_scoring($year = null) {
	    $year = is_null($year) ? Session::get('year', false) : intval($year);

	    // Get the competition start/end dates
	    $comp = Vid_competition::where(DB::raw("year(event_start)"), $year)->first();

        // Calculate day delta
	    if(Carbon::now()->gt($comp->event_end)) {
	        $delta_day = "Final";
	    } else {
	        $delta_day = "Day " . Carbon::now()->addDay()->diffInDays($comp->event_start);
	    }

		// Videos with score count
		if($year) {
			$videos = Video::where('flag', 0)->with('scores')->where(DB::raw("year(created_at)"), $year)->get();
		} else {
			$videos = Video::where('flag', 0)->with('scores')->get();
		}


        // Get counts for all score types
        $max = 0;
		foreach($videos as $video) {
		    $score = $video->all_scores_count();
			$scores[] = $score;
			$max = max(max(array_values($score)), $max);
		}
		$max += 3;

		// Create blank arrays to fill in
		$general = array_fill(0, $max, 0);
		$code    = array_fill(0, $max, 0);

		// Fill in histogram
		foreach($scores as $score) {
		    $general[$score['general']]++;
		    $code[$score['compute']]++;
		}

        // Generate Graph

	    JpGraph::load();
	    JpGraph::module('bar');

	     // Width and height of the graph
        $width = 575; $height = 350;

        // Create a graph instance
        $graph = new Graph($width,$height);
        $graph->ClearTheme();

        // Give room on the edges
        $graph->SetMargin(50,30,50,40);

        // Specify what scale we want to use,
        $graph->SetScale('textlin');

        // Setup a title for the graph
        $graph->title->Set("Video Scoring - $delta_day");
        $graph->title->SetFont(FF_ARIAL,FS_BOLD,14);
        $graph->title->SetMargin(15);

        // Setup titles and X-axis labels
        $graph->xaxis->SetTitle('Number of Scores', 'center');
        $graph->xaxis->SetTickLabels(array_keys($general));


        // Setup Y-axis title
        $graph->yaxis->title->Set('Number of Videos');

             // Create the bar graph
        $bp1 = new BarPlot($general);
        $bp2 = new BarPlot($code);

        // Style Bars
        $bp1->SetFillColor([79, 129, 189]); // Blue
        $bp2->SetFillColor([155, 187, 89]); // Green
        $bp1->SetColor('white@1.0');
        $bp2->SetColor('white@1.0');

        // Legend
        $bp1->SetLegend('General');
        $bp2->SetLegend('Code');

        // width
        $bp1->SetWidth(0.3);
        $bp2->SetWidth(0.3);

        $acc = new GroupBarPlot( [$bp1, $bp2] );
        // Add the plot to the graph
        $graph->Add($acc);


        // Display the graph
        $graph->Stroke();
	}

	public function graph_judge_scoring($year = 2015) {
        $year = intval($year);

        // Get the competition start/end dates
	    $comp = Vid_competition::where(DB::raw("year(event_start)"), $year)->first();

        // Calculate day delta
	    if(Carbon::now()->gt($comp->event_end)) {
	        $delta_day = "Final";
	    } else {
	        $delta_day = "Day " . Carbon::now()->addDay()->diffInDays($comp->event_start);
	    }

	    $video_count = Video::where('flag', 0)->where(DB::raw("year(created_at)"), $year)->count();

		// Judges Scoring Count
		$judge_list = Judge::with( [ 'video_scores' => function($q) use ($year) {
				if($year) {
					return $q->where(DB::raw("year(created_at)"), $year);
				} else {
					return $q;
				}
			} ] )->where('is_judge', true)->get();

		$judge_score_count = [];
		foreach($judge_list as $judge) {
			if(count($judge->video_scores)) {
				$judge_score_count[$judge->id]['general'] = $judge->video_scores->reduce(function($count, $score) { return ($score->score_group == 1) ? $count + 1 : $count; }, 0) / 3;
				$judge_score_count[$judge->id]['code'] = $judge->video_scores->reduce(function($count, $score) { return ($score->score_group == 3) ? $count + 1 : $count; }, 0);
			}
		}

        // Make Bins
        $bins = [ 0 ];
        for($i = 5; $i < $video_count; $i += 5) {
            $bins[] = $i;
        }
        $bins[] = $video_count;

        $general = array_fill(0,count($bins),0);
        $code = array_fill(0,count($bins),0);

		foreach($judge_score_count as $counts) {
		    // General
		    foreach($bins as $index => $bin) {
		        if($counts['general'] <= $bin) {
		            $general[$index]++;
		            break;
		        }
		    }
		    // Code
		    foreach($bins as $index => $bin) {
		        if($counts['code'] <= $bin) {
		            $code[$index]++;
		            break;
		        }
		    }
		}

        //dd($general, $code);

        // Generate Graph

	    JpGraph::load();
	    JpGraph::module('bar');

	     // Width and height of the graph
        $width = 575; $height = 350;

        // Create a graph instance
        $graph = new Graph($width,$height);
        $graph->ClearTheme();

        // Give room on the edges
        $graph->SetMargin(50,30,50,40);

        // Specify what scale we want to use,
        $graph->SetScale('textlin');

        // Setup a title for the graph
        $graph->title->Set("Judge Performace - $delta_day");
        $graph->title->SetFont(FF_ARIAL,FS_BOLD,14);
        $graph->title->SetMargin(15);

        // Setup titles and X-axis labels
        $graph->xaxis->SetTitle('Videos Scored', 'center');
        $graph->xaxis->SetTickLabels($bins);


        // Setup Y-axis title
        $graph->yaxis->title->Set('Number of Judges');

        // Create the bar graph
        $bp1 = new BarPlot($general);
        $bp2 = new BarPlot($code);

        // Style Bars
        $bp1->SetFillColor([79, 129, 189]); // Blue
        $bp2->SetFillColor([155, 187, 89]); // Green
        $bp1->SetColor('white@1.0');
        $bp2->SetColor('white@1.0');

        // Legend
        $bp1->SetLegend('General');
        $bp2->SetLegend('Code');

        // width
        $bp1->SetWidth(0.1);
        $bp2->SetWidth(0.1);

        $acc = new GroupBarPlot( [$bp1, $bp2] );
        // Add the plot to the graph
        $graph->Add($acc);


        // Display the graph
        $graph->Stroke();
	}

	public function graphs($year = null)
	{
	    Breadcrumbs::addCrumb('Graphs');
		View::share('title', 'Graphs');
	    $year = is_null($year) ? Session::get('year', false) : intval($year);

	    return View::make('video_scores.manage.graphs', compact('year'));
	}
}