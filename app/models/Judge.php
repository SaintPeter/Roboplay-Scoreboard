<?php

class Judge extends Eloquent {
	protected $guarded = array();
	public $timestamps = false;

	public static $rules = array(
		'username' => 'required',
		'display_name' => 'required',
		'email' => 'required'
	);

	public static function do_sync() {
		$judge = Judge::firstOrNew(array('ID' => Auth::user()->ID));

		$judge->ID = Auth::user()->ID;
		$judge->username = Auth::user()->user_login;
		$judge->email = Auth::user()->user_email;
		$judge->display_name = Usermeta::getName();

		$judge->save();
	}

}
