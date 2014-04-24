<?php

class Competition extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'description' => 'required',
		'location' => 'required',
		'address' => 'required',
		'event_date' => 'required'
	);

	public function divisions()
	{
		return $this->hasMany('Division')->orderBy('display_order', 'asc');
	}
}
