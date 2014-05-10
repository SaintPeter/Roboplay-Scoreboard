<?php

class Video_scores extends \Eloquent {
	protected $with = [ 'type' ]; // 'video',
	protected $guarded = [ 'id' ];

	public function division() {
		return $this->belongsTo('Vid_division', 'vid_division_id', 'id');
	}

	public function video() {
		return $this->hasOne('Video', 'id', 'video_id');
	}

	public function type() {
		return $this->hasOne('Vid_score_type', 'id', 'vid_score_type_id');
	}

	public function judge() {
		return $this->belongsTo('Judge');
	}

}