<?php

class Filetype extends \Eloquent {
	protected $guarded = ['id'];
	protected $table = 'filetype';
	public $timestamps = false;
	public static $rules = [
	    "ext" => 'required'
    ];

	public function files() {
		return $this->belongsToMany('Files');
	}

}