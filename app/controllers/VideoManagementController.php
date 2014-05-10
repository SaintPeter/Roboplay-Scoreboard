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

		return View::make('video_scores.manage.index', compact('videos','types'));

	}

	public function scores_csv() {
		$content = 'Name,County,School,"Challenge Teams","Video Teams","Competition","Division"' . "\n";

		$invoices = Wp_invoice::with('user', 'school', 'challenge_division', 'challenge_division.competition')->get();

		foreach($invoices as $invoice) {
			$invoice->user->metadata = $invoice->user->usermeta()->lists('meta_value', 'meta_key');
			$content .= '"' . join('","', [ $invoice->user->metadata['first_name'] . " " . $invoice->user->metadata['last_name'],
										   $invoice->school->district->county->name,
										   $invoice->school->name,
										   $invoice->team_count,
										   $invoice->video_count
										   ,
										   isset($invoice->challenge_division) ? $invoice->challenge_division->competition->name : 'Not Set',
										   isset($invoice->challenge_division) ? $invoice->challenge_division->name : 'Not Set'
										   ]) . '"' . "\n";
		}


		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="video_scores.csv'
		));
	}

}