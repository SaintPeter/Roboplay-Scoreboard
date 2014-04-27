<?php

class Wp_fix extends BaseController {

	public function user_schools() {
		$users = Wp_user::with('usermeta')->get();
		$counties = Counties::lists('name', 'county_id');

		foreach($users as $user) {
			$user->metadata = $user->usermeta()->lists('meta_value', 'meta_key');
			$user->metadata['wp_school'] = array_key_exists('wp_school',$user->metadata) ?  $user->metadata['wp_school'] : 'Not Set';
			$user->metadata['wp_district'] = array_key_exists('wp_district',$user->metadata) ?  $user->metadata['wp_district'] : 'Not Set';
			$user->metadata['wp_county'] = array_key_exists('wp_county',$user->metadata) ?  $user->metadata['wp_county'] : 'Not Set';
			$user->metadata['wp_school_id'] = array_key_exists('wp_school_id',$user->metadata) ?  $user->metadata['wp_school_id'] : 'Not Set';

			if(array_key_exists('wp_capabilities',$user->metadata)) {
				$roles = unserialize($user->metadata['wp_capabilities']);
				$user->metadata['role'] = array_key_exists('teachers',$roles) ?  'Teacher' : 'nonTeacher';
			}
		}

		return View::make('wp_fixes.user_school')
					->with(compact('users','counties'));
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

		return View::make('wp_fixes.invoice_fix')
					->with(compact('invoices'));
	}

	public function invoice_set() {
		Breadcrumbs::addCrumb('Invoice Payment Management','');
		$invoices = Wp_invoice::with('user', 'school', 'user.usermeta')->get();
		$divisions = Division::longname_array();

		foreach($invoices as $invoice) {
			$invoice->user->metadata = $invoice->user->usermeta()->lists('meta_value', 'meta_key');
		}

		return View::make('wp_fixes.invoice_set', compact('invoices', 'divisions'));
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
}
