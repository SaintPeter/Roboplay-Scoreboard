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
		} elseif($this->filetype->ext == 'doc' OR
		   $this->filetype->ext == 'docx' OR
		   $this->filetype->ext == 'xls' OR
		   $this->filetype->ext == 'xlsx') {
		   	return 'https://view.officeapps.live.com/op/view.aspx?src=' . urlencode(url($this->path()));
		} else {
		 	return url($this->path());
		}
	}

	public function full_path() {
		return public_path() . $this->path();
	}

	public function just_filename() {
	    return basename($this->filename, '.' . $this->filetype->ext);
	}

	public function rename($newFilename)
	{
	    // Validate the filename
	    if(preg_match('/[^a-zA-Z0-9_. -]/', $newFilename) OR empty($newFilename)) {
	        return false;
	    }
	    $new_path = join( DIRECTORY_SEPARATOR, [
	                public_path(),
	                "uploads",
	                "video_" . $this->video_id,
	                $newFilename . '.' . $this->filetype->ext
	                ]);
	    if(rename($this->full_path(), $new_path)) {
	        $this->filename = $newFilename . '.' . $this->filetype->ext;
	        $this->save();
	        return true;
	    } else {
	        return false;
	    }
	}
}