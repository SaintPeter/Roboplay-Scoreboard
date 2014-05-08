<?php

class Rubric extends \Eloquent {
	protected $table = 'rubric';
	protected $guarded = [ 'id' ];
	public $timestamps = false;
	

	public function vid_score_type()
	{
		return $this->hasOne('Vid_score_type');
	}

	public function competition() {
		return $this->hasOne('Vid_competition');
	}

}