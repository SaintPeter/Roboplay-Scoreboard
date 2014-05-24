<?php

class VideoManagementController extends \BaseController {

	public function index()
	{
		Breadcrumbs::addCrumb('Manage Scores');
		$video_scores = Video_scores::with('division', 'division.competition', 'judge')
							->orderBy('total', 'desc')
							->get();
		$videos = [];
		//dd(DB::getQueryLog());
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

		View::share('title', 'Manage Scores');
		return View::make('video_scores.manage.index', compact('videos','types'));

	}

	public function summary()
	{
		Breadcrumbs::addCrumb('Scoring Summary');
		View::share('title', 'Scoring Summary');

		// Videos with score count
		$videos = Video::with('scores')->get();

		foreach($videos as $video) {
			$output[$video->vid_division->competition->name][$video->vid_division->name][] = $video;
		}

		return View::make('video_scores.manage.summary', compact('output'));
	}

	// Display information about individual judges
	// as well as overall summary info
	public function judge_performance()
	{
		Breadcrumbs::addCrumb('Judge Performace');
		View::share('title', 'Judge Performance');

		// Judges Scoring Count
		$judge_list = Judge::with('video_scores')->where('is_judge', true)->get();

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

		return View::make('video_scores.manage.judge_performance', compact('judge_score_count'));
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
	public function by_video()
	{
		Breadcrumbs::addCrumb('Scores By Video');
		$video_scores = Video_scores::with('division', 'division.competition', 'judge')
							->orderBy('total', 'desc')
							->get();
		$videos = [];
		//dd(DB::getQueryLog());
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

		View::share('title', 'Manage Scores');
		return View::make('video_scores.manage.by_video', compact('videos','types'));

	}

	public function scores_csv() {
		$video_scores = Video_scores::with('division', 'division.competition', 'judge')
							->orderBy('total', 'desc')
							->get();
		$videos = [];
		//dd(DB::getQueryLog());
		$types = Vid_score_type::orderBy('id')->lists('name', 'id');
		$blank = array_combine(array_keys($types), array_fill(0, count($types), 0));
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

}