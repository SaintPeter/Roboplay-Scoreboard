<?php

class InvoiceReview extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /invoicereview
	 *
	 * @return Response
	 */
	public function invoice_review($year = 0)
	{
		Breadcrumbs::addCrumb('Invoice Review');
		View::share('title', 'Invoice Review');

	    $year = CompYear::yearOrMostRecent($year);

		$invoices = Invoices::with('wp_user', 'wp_user.usermeta', 'school',
		                           'videos', 'videos.vid_division', 'videos.students',
		                           'teams', 'teams.division', 'teams.students')
		                    ->where('year', $year)
		                    ->get();

		$last_sync = $invoices->max('updated_at');

        // Callback for reduce to get a total student count
        $student_count = function($curr, $next) {
            return $curr + $next->students->count();
        };

        $comp_year = CompYear::where('year', $year)
                                     ->with('vid_divisions', 'divisions')
                                     ->first();

        $vid_division_list = $comp_year->vid_divisions->lists('name', 'id');
        $division_list = $comp_year->divisions->lists('name','id');

		return View::make('invoice_review.index',
		                compact('invoices', 'year',
		                        'student_count', 'last_sync',
		                        'vid_division_list', 'division_list'));
	}

	public function toggle_video($video_id) {
	    $video = Video::findOrFail($video_id);
	    $video->update(['audit' => !$video->audit ]);
	    return 'true';
	}

	public function save_video_notes($video_id) {
	    $video = Video::findOrFail($video_id);
	    $video->update(['notes' => Input::get('notes', '') ]);
	    return 'true';
	}

	public function save_video_division($video_id, $vid_div_id) {
	       $video = Video::findOrFail($video_id);
	    $video->update(['vid_division_id' => $vid_div_id ]);
	    return 'true';
	}


	public function invoice_sync($year = 0)
	{
	    $comp_year = CompYear::where('year', $year)->firstOrFail();

	    // C-STEM Invoices (2014-2016)
	    if($comp_year->invoice_type == 1) {

    	    $raw_invoices = Wp_invoice_table::with('invoice_data', 'user', 'user.usermeta')
    									->where('invoice_type_id', $comp_year->invoice_type_id)->get();

            $count = 0;
            foreach($raw_invoices as $raw_invoice) {
                //dd('<pre>' . print_r($raw_invoice->toArray(), true) . '</pre>');
                // Fetch a local invoice if it exists
                $invoice = Invoices::firstOrNew([
                    'remote_id' => $raw_invoice->invoice_no,
                    'user_id' => $raw_invoice->user->ID,
                    'year' => $year
                ]);

                $invoice->wp_school_id = intval($raw_invoice->user->getMeta('wp_school_id',0));

                $invoice->team_count = $raw_invoice->getData('Challenge', 0) + $raw_invoice->getData('Challenge2', 0);
                $invoice->video_count = $raw_invoice->getData('Video', 0);
                $invoice->math_count = $raw_invoice->getData('PreMath', 0) + $raw_invoice->getData('AlgMath', 0);

                $invoice->paid = $raw_invoice->paid;

                $invoice->save();
                $count++;
            }
            return Redirect::route('invoice_review', $year)->with('message', 'Synced ' . $count . " Invoices for $year");
        }
	}
}