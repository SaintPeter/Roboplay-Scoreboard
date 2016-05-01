<?php

class RandomListElement extends \Eloquent {
	protected $fillable = [ 'd1', 'd2', 'd3', 'd4', 'd5', 'random_list_id'];

	public $timestamps = false;

	// Relationships
	public function vid_division()
	{
		return $this->belongsTo('RandomList');
	}

}