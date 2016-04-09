<?php

class InvoiceReview extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /invoicereview
	 *
	 * @return Response
	 */
	public function invoice_review($year)
	{
		Breadcrumbs::addCrumb('Invoice Review');
		View::share('title', 'Invoice Review');
		$invoices = Wp_invoice_table::with('invoice_data', 'videos', 'videos.vid_division', 'videos.students','user', 'user.usermeta', 'teams', 'teams.students')
									->where('invoice_type_id', 16)->get();
		//dd($invoices);
		return View::make('invoice_review.index')->with(compact('invoices'));
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

                $invoice->teams = $raw_invoice->getData('Challenge', 0) + $raw_invoice->getData('Challenge2', 0);
                $invoice->videos = $raw_invoice->getData('Video', 0);
                $invoice->math = $raw_invoice->getData('PreMath', 0) + $raw_invoice->getData('AlgMath', 0);

                $invoice->paid = $raw_invoice->paid;

                $invoice->save();
                $count++;
            }
            return Redirect::route('invoice_review', $year)->with('message', 'Synced ' . $count . " Invoices for $year");
        }
	}
}