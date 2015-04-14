<?php

class Video extends Eloquent {
	protected $guarded = array();
	protected $with = [ 'school', 'school.district', 'school.district.county', 'files', 'vid_division' ];

	public static $rules = array(
		'name' => 'required',
		'yt_code' => array('required','yt_valid', 'yt_embeddable', 'yt_public'),
		'students' => 'required',
		'school_id' => 'required',
		'vid_division_id' => 'required'
	);

	protected $attributes = array(
  		'has_custom' => false,
  		'has_vid' => false,
  		'has_code' => false
	);

	public static function boot()
    {
        parent::boot();

        static::deleting(function($video)
        {
        	foreach($video->files as $file) {
        		$file->delete();
        	}

           if(is_dir(public_path() . '/uploads/video_' . $video->id)) {
            	rmdir(public_path() . '/uploads/video_' . $video->id);
            }
        });
    }


	public function setYtCodeAttribute($code)
	{
		if(preg_match('#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#', $code, $matches)) {
			if(isset($matches[2]) && $matches[2] != ''){
				$this->attributes['yt_code'] = $matches[2];
				return;
			} else {
				$this->attributes['yt_code'] = '';
			}
		}
		$this->attributes['yt_code'] = $code;
	}

	// Relationships
	public function vid_division()
	{
		return $this->belongsTo('Vid_division');
	}

	public function school()
	{
		return $this->belongsTo('Schools', 'school_id', 'school_id');
	}

	public function files()
	{
		return $this->hasMany('Files');
	}

	public function scores()
	{
		return $this->hasMany('Video_scores');
	}

	public function students() {
		return $this->morphToMany('Student', 'studentable');
	}

	// Methods
	public function student_count()
	{
		return $this->students()->count();
	}

	public function student_list()
	{
		return "Not Implmented Yet";
	}

	public function general_scores_count()
	{
		$count = 0;
		$this->scores->map(function($score) use (&$count) {
			if($score->score_group == 1) {
				$count++;
			}
		});
		$count /= 3;
		return $count;
	}

	public function part_scores_count()
	{
		if($this->has_custom) {
			$count = 0;
			$this->scores->map(function($score) use (&$count) {
				if($score->score_group == 2) {
					$count++;
				}
			});
			return $count;
		}
		return '-';
	}

	public function compute_scores_count()
	{
		if($this->has_code) {
			$count = 0;
			$this->scores->map(function($score) use (&$count) {
				if($score->score_group == 3) {
					$count++;
				}
			});
			return $count;
		}
		return '-';
	}

}
