<?php

class InvoiceReview extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /invoicereview
	 *
	 * @return Response
	 */
	public function invoice_review()
	{
		Breadcrumbs::addCrumb('Invoice Review');
		View::share('title', 'Invoice Review');
		$invoices = Wp_invoice_table::with('invoice_data', 'videos', 'videos.vid_division', 'videos.students','user', 'user.usermeta', 'teams', 'teams.students')->where('invoice_type_id', 16)->get();
		//dd($invoices);
		return View::make('invoice_review.index')->with(compact('invoices'));
	}
}