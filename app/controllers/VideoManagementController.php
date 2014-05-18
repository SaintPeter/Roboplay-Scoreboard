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