<?php

class Video_comment extends \Eloquent {
	protected $guarded = [ 'id' ];
	protected $table = 'Video_comment';

	public function video() {
		return $this->hasOne('Video');
	}

	public function judge() {
		return $this->hasOne('Judge');
	}
}