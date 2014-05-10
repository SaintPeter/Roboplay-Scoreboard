<?php

class Filetype extends \Eloquent {
	protected $guarded = [];
	protected $table = 'filetype';
	public $timestamps = false;

	public function files() {
		return $this->belongsToMany('Files');
	}

}