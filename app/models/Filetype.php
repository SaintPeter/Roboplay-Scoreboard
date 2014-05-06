<?php

class Filetype extends \Eloquent {
	protected $fillable = [];
	protected $table = 'filetype';
	public $timestamps = false;

	public function files() {
		return $this->belongsToMany('Files');
	}

}