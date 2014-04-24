<?php

class Score_element extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'display_text' => 'required',
		'element_number' => 'required',
		'base_value' => 'required',
		'multiplier' => 'required',
		'min_entry' => 'required',
		'max_entry' => 'required',
		'type' => 'required',
		'challenge_id' => 'required'
	);

	public function challenge()
	{
		return belongsTo('Challenge');
	}

	public function judge()
	{
		return hasOne('Judge');
	}
}
