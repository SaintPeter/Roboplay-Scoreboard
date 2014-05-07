<?php

class Vid_score_type extends \Eloquent {
	protected $guarded = [ 'id' ];
	public $timestamps = false;

	public function video_scores() {
		return $this->belongsToMany('Video_scores');
	}

	public function rubric() {
		return $this->belongsToMany('Rubric');
	}

	public function competition() {
		return $this->hasOne('Vid_competition');
	}
}