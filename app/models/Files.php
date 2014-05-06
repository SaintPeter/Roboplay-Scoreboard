<?php

class Files extends \Eloquent {
	protected $guarded = [ 'id' ];
	protected $with = [ 'filetype' ];

	public static function boot()
    {
        parent::boot();

        static::deleted(function($file)
        {
            if(is_file(public_path() . $file->path())) {
            	unlink(public_path() . $file->path());
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

}