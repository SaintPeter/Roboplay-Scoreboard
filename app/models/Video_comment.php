<?php

class Video_comment extends \Eloquent {
	protected $guarded = [ 'id' ];
	protected $table = 'Video_comment';

	public function video() {
		return $this->belongsTo('Video');
	}

	public function judge() {
		return $this->belongsTo('Judge');
	}
}