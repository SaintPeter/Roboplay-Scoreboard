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
			$user->metadata['school_id'] = array_key_exists('school_id',$user->metadata) ?  $user->metadata['school_id'] : 'Not Set';
		}

		return View::make('wp_fixes.user_school')
					->with(compact('users','counties'));
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

}
