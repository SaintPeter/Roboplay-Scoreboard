<?php

class Vid_competition extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'event_start' => 'required|date',
		'event_end' => 'required|date'
	);

	public function divisions() {
		return $this->hasMany('Vid_division');
	}
}
