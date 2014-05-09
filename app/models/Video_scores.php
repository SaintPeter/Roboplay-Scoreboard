<?php

class Video_scores extends \Eloquent {
	protected $with = [ 'video', 'type' ];
	protected $guarded = [ 'id' ];

	public function division() {
		return $this->hasOne('Vid_division');
	}

	public function video() {
		return $this->hasOne('Video', 'id', 'video_id');
	}

	public function type() {
		return $this->hasOne('Vid_score_type', 'id', 'vid_score_type_id');
	}

}