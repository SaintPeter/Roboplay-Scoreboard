<?php

class Files extends \Eloquent {
	protected $guarded = [ 'id' ];
	protected $with = [ 'filetype' ];

	public static function boot()
    {
        parent::boot();

        static::deleted(function($file)
        {
            if(is_file($file->full_path())) {
            	unlink($file->full_path());
            }
        });
    }

	public function video() {
		return $this->belongTo('Video');
	}

	public function filetype() {
		return $this->hasOne('Filetype', 'id', 'filetype_id');
	}

	public function path() {
		return "/uploads/video_" . $this->video_id . "/" . $this->filename;
	}

	public function url() {
		if($this->filetype->type == 'code') {
			return route('file_viewer', [ 'file_id' => $this->id ]);
		} else {
		 	return $this->path();
		}
	}

	public function full_path() {
		return public_path() . $this->path();
	}

}