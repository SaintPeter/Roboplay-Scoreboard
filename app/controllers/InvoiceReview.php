<?php

class InvoiceReview extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /invoicereview
	 *
	 * @return Response
	 */
	public function invoice_review($year = 0, $terse = 0)
	{
		Breadcrumbs::addCrumb('Invoice Review');
		View::share('title', 'Invoice Review');

	    $year = CompYear::yearOrMostRecent($year);
	    $comp_years = CompYear::orderBy('year')->get();

		$invoices = Invoices::with('wp_user', 'wp_user.usermeta', 'judge', 'school')
	                        ->with( [ 'videos' => function($q) use ($year) {
	                             return $q->where('year', $year);
	                        }, 'videos.students', 'videos.vid_division'])
	                        ->with( [ 'teams' => function($q) use ($year) {
	                             return $q->where('year', $year);
	                        }, 'teams.students', 'teams.students.math_level', 'teams.division'])
    	                    ->where('year', $year)
    	                    ->get();

		$last_sync_date = $invoices->max('updated_at');
		if(isset($last_sync_date)) {
		    $last_sync = $last_sync_date->format('D, F j, g:s a');
		} else {
		    $last_sync = "Never";
		}

        // Callback for reduce to get a total student count
        $student_count = function($curr, $next) {
            return $curr + $next->students->count();
        };

        $comp_year = CompYear::where('year', $year)
                                     ->with('vid_divisions', 'divisions')
                                     ->first();

        $vid_division_list = $comp_year->vid_divisions->lists('name', 'id');

        foreach($comp_year->divisions as $division) {
            $division_list[$division->competition->name][$division->id] = $division->longname();
        }
        //$division_list = $comp_year->divisions->lists('name','id');

		if($terse) {
			return View::make('invoice_review.usernames',
						compact('invoices', 'year',
		                        'student_count', 'last_sync',
		                        'vid_division_list', 'division_list',
		                        'comp_years'));
		}

		return View::make('invoice_review.index',
		                compact('invoices', 'year',
		                        'student_count', 'last_sync',
		                        'vid_division_list', 'division_list',
		                        'comp_years'));
	}

	public function toggle_video($video_id) {
	    $video = Video::findOrFail($video_id);
	    $video->update(['audit' => !$video->audit ]);
	    return 'true';
	}

	public function toggle_team($team_id) {
	    $team = Team::findOrFail($team_id);
	    $team->update(['audit' => !$team->audit ]);
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

	public function save_team_division($team_id, $div_id) {
	    $team = Team::findOrFail($team_id);
	    $team->update(['division_id' => $div_id ]);
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

            // Check for removed invoices
            $invoices = Invoices::where('year', $year)->get();
            $raw_invoice_array = $raw_invoices->lists('invoice_no');
            $removed = 0;
            foreach($invoices as $invoice) {
                if(!in_array($invoice->remote_id, $raw_invoice_array)) {
                    $invoice->delete();
                    $removed++;
                }
            }

            return Redirect::route('invoice_review', $year)->with('message', 'Synced ' . $count . " Invoices, Removed $removed for $year");
        }
	}

	// Data Export interface
	public function data_export($year = '')
	{
	    Breadcrumbs::addCrumb('Data Export');
		View::share('title', 'Data Export');

		// Load a year from session if it's not set in the URL
		if(!$year) {
			$year = Session::get('year', $year);
		}

	    return View::make('data_export.index', compact('year'));
	}

	public function student_tshirts_csv($year = '')
	{
	    if(!$year) {
	        return Redirect::route('data_export')->with('message', 'Year must be set to export data');
	    }


		$invoices = Invoices::with('wp_user', 'wp_user.usermeta', 'judge', 'school')
	                        ->with( [ 'teams' => function($q) use ($year) {
	                             return $q->where('year', $year);
	                        }, 'teams.students', 'teams.division', 'teams.division.competition'])
    	                    ->where('year', $year)
    	                    ->get();

        // Header
	    $content = "Teacher Name, Site, Team Name, Student Name, Size\n";

		foreach($invoices as $invoice) {
		    foreach($invoice->teams as $team) {
		        foreach($team->students as $student) {
        			$content .= '"';
        			$content .= join('","',
        			                [ $invoice->judge->display_name,
	                                $team->division->competition->location,
	                                $team->name,
	                                $student->fullName(),
	                                ($student->tshirt) ? $student->tshirt : 'Not Selected'
								   ]) .
						     '"' . "\n";
				}
			}
		}

		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="student_tshirts_' . $year . '.csv"'
		));
	}

	public function teacher_tshirts_csv($year = '')
	{
	    if(!$year) {
	        return Redirect::route('data_export')->with('message', 'Year must be set to export data');
	    }


		$invoices = Invoices::with('wp_user', 'wp_user.usermeta', 'judge', 'school')
	                        ->with( [ 'teams' => function($q) use ($year) {
	                             return $q->where('year', $year);
	                        }, 'teams.division', 'teams.division.competition'])
    	                    ->where('year', $year)
    	                    ->where('team_count', '>', 0)
    	                    ->get();

        // Header
	    $content = "Teacher Name, Site, Size\n";

		foreach($invoices as $invoice) {
		    $team = $invoice->teams->first();
		    if($team) {
    			$content .= '"';
    			$content .= join('","',
    			                [ $invoice->judge->display_name,
                                $team->division->competition->location,
                                ($invoice->judge->tshirt) ? $invoice->judge->tshirt : 'Not Selected',
    						   ]) .
    				     '"' . "\n";
    	    }
		}

		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="teacher_tshirts_' . $year . '.csv"'
		));

	}

	public function teacher_teams_csv($year = '')
	{
	    if(!$year) {
	        return Redirect::route('data_export')->with('message', 'Year must be set to export data');
	    }


		$invoices = Invoices::with('wp_user', 'wp_user.usermeta', 'judge', 'school')
	                        ->with( [ 'teams' => function($q) use ($year) {
	                             return $q->where('year', $year);
	                        }, 'teams.students', 'teams.division', 'teams.division.competition'])
    	                    ->where('year', $year)
    	                    ->get();

        // Header
	    $content = "Teacher Name,School Name,Team Name,Site,Division, Level\n";

		foreach($invoices as $invoice) {
		    foreach($invoice->teams as $team) {
    			$content .= '"';
    			$content .= join('","',
    			                [
    			                $invoice->wp_user->getName(),
    			                ($invoice->school) ? $invoice->school->name : "(Not Set)",
                                $team->name,
                                $team->division->competition->location,
                                $team->division->name,
                                $team->division->level
							   ]) .
					     '"' . "\n";
			}
		}

		// return an string as a file to the user
		return Response::make($content, '200', array(
			'Content-Type' => 'application/octet-stream',
			'Content-Disposition' => 'attachment; filename="teacher_teams_' . $year . '.csv"'
		));
	}

}