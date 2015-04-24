<?php

class Wp_user extends Eloquent {
	public $connection = 'mysql-wordpress';
	protected $table = 'users';
	protected $primaryKey = 'ID';
	protected $guarded = array('ID');
	public $timestamps = false;

	public static $rules = array();

	public $metadata = [];

	public function usermeta() {
		return $this->hasMany('Usermeta', 'user_id');
	}

	public function getName() {
		return $this->getMeta('first_name') . ' ' . $this->getMeta('last_name');
	}

	public function getSchool() {
		if($this->getMeta('wp_school_id', false)) {
			return Schools::find($this->getMeta('wp_school_id'))->name;
		} else {
			return "No School Set";
		}
	}

	public function getMeta($key, $default = '') {
		if(empty($this->metadata)) {
			$this->metadata = $this->usermeta->lists('meta_value', 'meta_key');
		}
		if(array_key_exists($key, $this->metadata)) {
			return $this->metadata[$key];
		} else {
			return $default;
		}

	}

}