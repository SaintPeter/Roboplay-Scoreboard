<?php

class Video extends Eloquent {
	protected $guarded = [ 'id', 'flag' ];
	protected $with = [ 'school', 'files', 'vid_division' ];

	public static $rules = array(
		'name' => 'required',
		'yt_code' => array('required','yt_valid', 'yt_embeddable', 'yt_public'),
		'school_id' => 'required',
		'vid_division_id' => 'required|not_in:0'
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
	public function awards() {
	    return $this->belongsToMany('VideoAward');
	}

	public function vid_division()
	{
		return $this->belongsTo('Vid_division');
	}

	public function division()
	{
		return $this->belongsTo('Vid_division', 'vid_division_id');
	}

	public function school()
	{
		return $this->belongsTo('School');
	}

	public function files()
	{
		return $this->hasMany('Files')->orderBy('filename');
	}

	public function scores()
	{
		return $this->hasMany('Video_scores');
	}

	public function students() {
		return $this->morphToMany('Student', 'studentable');
	}

	public function comments() {
		return $this->hasMany('Video_comment');
	}

	public function teacher() {
		return $this->belongsTo('Wp_user', 'teacher_id', 'ID');
	}

	// Methods
	public function student_count()
	{
		return $this->students()->count();
	}

	public function student_list()
	{
		$student_list = [];

		if(count($this->students) > 0) {
			foreach($this->students as $student) {
				$student_list[] = $student->fullName();
			}
		} else {
			$student_list = [ 'No Students' ];
		}

		return $student_list;
	}

	public function general_scores_count()
	{
		$count = $this->scores->reduce(function($acc, $score) {
			if($score->score_group == 1) {
				$acc++;
			}
		    return $acc;
		}, 0);
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

	public function all_scores_count()
	{
	    $count = $this->scores->reduce(function($acc, $score) {
			if($score->score_group == 1) {
				$acc['general']++;
			}
			if($score->score_group == 2) {
				$acc['custom']++;
			}
			if($score->score_group == 3) {
				$acc['compute']++;
			}
		    return $acc;
		}, ['general' => 0, 'compute' => 0, 'custom' => 0]);

		$count['general'] /= 3;
		return $count;
	}

    // Produce a list of files sorted into categories
	public function getFilelistAttribute()
	{
	    $output = [];

	    if(count($this->files)) {
	        foreach($this->files as $file) {
	            $output[$file->filetype->name][] = $file;
	        }
	        uksort($output, function($a, $b) { return strcasecmp($a, $b); });
	        foreach($output as $cat => $file) {
	            uasort($output[$cat], function($a, $b) { return strnatcasecmp($a->filename, $b->filename); } );
	        }
	    }
	    return $output;
	}

}
