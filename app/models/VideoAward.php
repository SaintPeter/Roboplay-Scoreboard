<?php

class VideoAward extends \Eloquent {
	protected $fillable = ['name'];
	public $timestamps = false;

    public function videos() {
	    return $this->belongsToMany('Video');
	}

}