<?php

class Video_comment extends \Eloquent {
	protected $guarded = [ 'id' ];
	protected $table = 'video_comment';

	public function video() {
		return $this->belongsTo('Video');
	}

	public function judge() {
		return $this->belongsTo('Judge');
	}
}