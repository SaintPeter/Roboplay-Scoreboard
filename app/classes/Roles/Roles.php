<?php

namespace Roles;

class Roles {

	public static function isAdmin()
	{
		return Roles::is('administrator');
	}

	public static function isJudge()
	{
		return Roles::is('judges','administrator');
	}

	public static function isTeacher()
	{
		return Roles::is('teachers','administrator');
	}

	public static function is()
	{
		static $roles = array();

		// Check for args
		if(func_num_args() == 0) {
			return false;
		}

		// Must be logged in
		if(\Auth::guest()) {
			return false;
		}

		$args = func_get_args();

		// Check to see if we've gotten the roles before
		if(empty($roles)) {
			$query = \Usermeta::where('user_id', '=', \Auth::user()->ID)->where('meta_key', '=', 'wp_capabilities')->first();
			if(empty($query)) {
				// User has no roles
				$roles[] = 'empty';
				return false;
			} else {
				// We have the roles, save them
				$roles = unserialize($query->meta_value);
			}
		}

		// Check to see if the user has the role
		foreach($args as $roleCheck)
		{
			if(array_key_exists($roleCheck, $roles)) {
				return true;
			}
		}

		// If key is not set, return false
		return false;

	}

}