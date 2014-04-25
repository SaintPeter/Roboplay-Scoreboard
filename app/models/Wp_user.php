<?php

class Wp_user extends Eloquent {
	public $connection = 'mysql-wordpress';
	protected $table = 'users';
	protected $primaryKey = 'ID';
	protected $guarded = array('ID');

	public static $rules = array();
	
	public $metadata = [];
	
	public function usermeta() {
		return $this->hasMany('Usermeta', 'user_id');
	}
}