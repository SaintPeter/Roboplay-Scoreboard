<?php

class Wp_fix extends BaseController {

	public function user_schools() {
		$users = Wp_user::with('usermeta')->get();
		$counties = Counties::lists('name', 'county_id');
		
		foreach($users as $user) {
			$user->metadata = $user->usermeta()->lists('meta_value', 'meta_key');
		}
		
		
		return View::make('wp_fixes.user_school')
					->with(compact('users','counties'));
	}
	
	public function ajax_districts($county_id) {
		$district_list = Districts::where('county_id', $county_id)->get();
		foreach($district_list as $district) {
			$output[] = [ 'id' => $district->district_id, 'value' => $district->name ];
		}
		return json_encode($output);
	}
	
	public function ajax_schools($district_id) {
		$school_list = Schools::where('district_id', $district_id)->get();
		foreach($school_list as $school) {
			$output[] = [ 'id' => $school->school_id, 'value' => $school->name ];
		}
		return json_encode($output);
		
	}

}
