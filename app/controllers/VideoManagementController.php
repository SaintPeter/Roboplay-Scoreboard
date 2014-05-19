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
		$videos = Video::with('scores')->get();

		foreach($videos as $video) {
			$output[$video->vid_division->competition->name][$video->vid_division->name][] = $video;
		}

		return View::make('video_scores.manage.summary', compact('output'));
	}
	
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
				return Redirect::route('video_scores.manage.index')->with('message', 'No Score Type Selected');
		}
		
		$affectedRows = 0;
		foreach($select as $judge_id => $video_list) {
			$affectedRows += Video_scores::where('judge_id', $judge_id)
										 ->whereIn('video_id', $video_list)
										 ->whereIn('score_group', $groups)
										 ->delete();
		}
		
		return Redirect::route('video_scores.manage.index')
					    ->with('message', "Deleted $affectedRows scores");
	}

	public function scores_csv() {
		$content = 'Name,County,School,"Challenge Teams","Video Teams","Competition","Division"' . "\n";

		$videos = Video::with('scores')->get();


		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="video_scores.csv'
		));
	}

}