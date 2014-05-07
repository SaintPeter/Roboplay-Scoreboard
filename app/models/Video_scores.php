<?php

class Video_scores extends \Eloquent {
	protected $guarded = [ 'id' ];

	public function division() {
		return $this->hasOne('Vid_division');
	}

	public function video() {
		return $this->hasOne('Video');
	}

}