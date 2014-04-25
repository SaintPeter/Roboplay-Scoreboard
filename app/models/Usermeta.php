<?php

class Usermeta extends Eloquent {
	public $connection = 'mysql-wordpress';
	protected $table = 'usermeta';
	protected $primaryKey = 'umeta_id';
	protected $guarded = array('umeta_id');
	public $timestamps = false;

	public static $rules = array();

	public static function getSchoolName()
	{
		if(!Auth::guest() AND Roles::isTeacher())
		{
			return Usermeta::where('user_id', Auth::user()->ID)->where('meta_key', 'wp_school')->pluck('meta_value');
		} else {
			return '';
		}
	}

	public static function getName()
	{
		if(!Auth::guest())
		{
			$first = Usermeta::where('user_id', Auth::user()->ID)->where('meta_key', 'first_name')->pluck('meta_value');
			$last = Usermeta::where('user_id', Auth::user()->ID)->where('meta_key', 'last_name')->pluck('meta_value');
			return $first . ' ' . $last;
		} else {
			return '';
		}
	}
}
