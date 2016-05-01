<?php

class RandomList extends \Eloquent {
	protected $fillable = [
	    'name', 'format', 'popup_format',
	    'd1', 'd2', 'd3', 'd4', 'd5',
	    'display_order',
	    'challenge_id'];

    public static $rules = [
		'name' => 'required',
		'format' => 'required',
	    'popup_format' => 'required',
		'display_order' => 'numeric'
	];


	// Relationships
	public function elements()
	{
	    return $this->hasMany('RandomListElement');
	}

}