<?php

class Wp_fix extends BaseController {

	public function user_schools() {
		$invoices = Wp_invoice_table::where('invoice_type_id', 16)->with('user', 'user.usermeta')->get();
		$counties = Counties::lists('name', 'county_id');

		Breadcrumbs::addCrumb('Set Users School','');
		View::share('title', 'Set Users School');
		return View::make('wp_fixes.user_school')
					->with(compact('invoices','counties'));
	}

	public function invoice_fix() {
		$invoices = Wp_invoice::with('user', 'school')->with([ 'user.usermeta' => function($query) {
				$query->where('meta_key', 'wp_school_id');
			}])->get();

		foreach($invoices as $invoice) {
			if($invoice->school_id == 0 AND !$invoice->user->usermeta->isEmpty()) {
				$invoice->school_id = $invoice->user->usermeta->first()->meta_value;
				$invoice->save();
			}
			$invoice->user->metadata['fullname'] = Usermeta::getFullName($invoice->user_id);
		}
		Breadcrumbs::addCrumb('Fix Invoices','');
		View::share('title', 'Fix Invoices');
		return View::make('wp_fixes.invoice_fix')
					->with(compact('invoices'));
	}

	public function invoice_set() {
		Breadcrumbs::addCrumb('Invoice Payment Management','');
		$invoices = Wp_invoice::with('user', 'school', 'user.usermeta')->get();
		$divisions = Division::longname_array();
		$vid_divisions = Vid_division::longname_array();

		foreach($invoices as $invoice) {
			$invoice->user->metadata = $invoice->user->usermeta()->lists('meta_value', 'meta_key');
			$roles = unserialize($invoice->user->metadata['wp_capabilities']);
			//dd($roles);
			$invoice->user->metadata['is_teacher'] = array_key_exists('teachers', $roles);
		}
		View::share('title', 'Invoice Management');
		return View::make('wp_fixes.invoice_set', compact('invoices', 'divisions', 'vid_divisions'));
	}

	public function invoice_csv() {
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
			'Content-Disposition' => 'attachment; filename="invoices.csv'
		));
	}

	public function division_check() {
		Breadcrumbs::addCrumb('Video Division Check','');
		//$videos = Video::all();
		$invoices = Wp_invoice::with('videos', 'judge', 'vid_division', 'school')
						->where('video_count', '>', 0)
						->where('paid', 1)
						->orderBy('school_id')->get();

		//dd(DB::getQueryLog());

		View::share('title', 'Video Division Check');
		return View::make('wp_fixes.division_check', compact('invoices'));

	}

	public function team_division_check() {
		Breadcrumbs::addCrumb('Team Division Check','');
		//$videos = Video::all();
		$invoices = Wp_invoice::with('judge', 'challenge_division', 'school')
						->where('team_count', '>', 0)
						->where('paid', 1)
						->orderBy('school_id')->get();

		foreach($invoices as $invoice) {
			$div_id = $invoice->division_id;
			$invoice->load([ 'teams' => function($q) use($div_id) {
				return $q->where('division_id', $div_id);
			} ], 'teams.division');
		}

		//dd(DB::getQueryLog());

		View::share('title', 'Team Division Check');
		return View::make('wp_fixes.team_division_check', compact('invoices'));

	}

	public function ajax_set_paid($invoice_no, $value) {
		$invoice = Wp_invoice::find($invoice_no);
		$invoice->paid = $value;
		$invoice->save();

		return 'true';
	}

	public function ajax_set_div($invoice_no, $value) {
		$invoice = Wp_invoice::find($invoice_no);
		$invoice->division_id = $value;
		$invoice->save();

		return 'true';
	}

	public function ajax_set_vid_div($invoice_no, $value) {
		$invoice = Wp_invoice::find($invoice_no);
		$invoice->vid_division_id = $value;
		$invoice->save();

		return 'true';
	}

	public function ajax_counties() {
		$counties_list = Counties::all();
		foreach($counties_list as $county) {
			$output[] = [ 'id' => $county->county_id, 'value' => $county->name ];
		}
		$data = json_encode($output);
    	// convert into JSON format and print
    	$response = isset($_GET['callback'])?$_GET['callback']."(".$data.")":$data;
		return $response;
	}

	public function ajax_districts($county_id) {
		$district_list = Districts::where('county_id', $county_id)->get();
		foreach($district_list as $district) {
			$output[] = [ 'id' => $district->district_id, 'value' => $district->name ];
		}
		$data = json_encode($output);
    	// convert into JSON format and print
    	$response = isset($_GET['callback'])?$_GET['callback']."(".$data.")":$data;
		return $response;
	}

	public function ajax_schools($district_id) {
		$school_list = Schools::where('district_id', $district_id)->get();
		foreach($school_list as $school) {
			$output[] = [ 'id' => $school->school_id, 'value' => $school->name ];
		}
		$data = json_encode($output);
    	// convert into JSON format and print
    	$response = isset($_GET['callback'])?$_GET['callback']."(".$data.")":$data;
		return $response;
	}

	public function ajax_save_school() {
		$user_id = $_POST['user_id'];
		$school_id = $_POST['select_school'];

		$school = Schools::with('district', 'district.county')->find($school_id);

		$um = Usermeta::firstOrNew(['user_id' => $user_id, 'meta_key' => 'wp_school_id']);
		$um->meta_value = $school_id;
		$um->save();

		$um = Usermeta::firstOrNew(['user_id' => $user_id, 'meta_key' => 'wp_school']);
		$um->meta_value = $school->name;
		$um->save();

		$um = Usermeta::firstOrNew(['user_id' => $user_id, 'meta_key' => 'wp_district']);
		$um->meta_value = $school->district->name;
		$um->save();

		$um = Usermeta::firstOrNew(['user_id' => $user_id, 'meta_key' => 'wp_county']);
		$um->meta_value = $school->district->county->name;
		$um->save();

		return "true";
	}

	// Fix that students were no assigned a school_id initially
	public function student_fix_school() {
		$students = Student::with('teacher', 'teacher.usermeta')->get();
		foreach($students as $student) {
			$school_id = $student->teacher->getMeta('wp_school_id');
			$student->school_id = $school_id;
			echo $student->fullName() . ' Set to: ' . $school_id . '<br />';
			$student->save();
		}
	}

	// Switch to a different user
	public function switch_user($user_id) {
		if(Roles::isAdmin()) {
			//$user = Wp_user::with('usermeta')->findOrFail($user_id);
			Auth::logout();
			Auth::loginUsingId($user_id);
			return Redirect::to('/')->with('message', 'Logged in');
		}
		return Redirect::to('/')->with('message', 'You do not have permission to do that.');
	}

	// List all Judges
	public function list_judges() {
		$judges = Judge::all();
		Breadcrumbs::addCrumb('List Users','');

		View::share('title', 'List Users');
		return View::make('wp_fixes.list_judges')->with(compact('judges'));
	}

	// List all students
	public function student_list() {
		$students = Student::with('teams', 'teams.division','teams.division.competition', 'school','teacher', 'teacher.usermeta')->get();

		foreach($students as $student) {
			$team = ($student->teams()->first()) ? $student->teams()->first()->division->competition->name : 'No Team';
			echo $student->fullName() . "," . $student->school->name . "," . $student->teacher->getName() . ",$team" . "<br />";

		}

	}

}
