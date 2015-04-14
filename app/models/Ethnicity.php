<?php

class Ethnicity extends \Eloquent {
	protected $table = 'ethnicities';
	protected $guarded = array('id');
	public $timestamps = false;

	// Relationships
	public function students() {
		return $this->hasMany('Student');
	}
}