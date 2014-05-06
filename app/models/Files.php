<?php

class Files extends \Eloquent {
	protected $guarded = [ 'id' ];
	protected $with = [ 'filetype' ];

	public function video() {
		return $this->belongTo('Video');
	}

	public function filetype() {
		return $this->hasOne('Filetype');
	}

}