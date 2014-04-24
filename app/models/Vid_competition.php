<?php

class Vid_competition extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'event_date' => 'required'
	);

	public function divisions() {
		return $this->hasMany('Vid_division');
	}
}
