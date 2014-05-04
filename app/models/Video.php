<?php

class Video extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'yt_code' => array('required','yt_valid', 'yt_embeddable', 'yt_public'),
		'students' => 'required',
		'school_id' => 'required',
		'vid_division_id' => 'required'
	);

	protected $attributes = array(
  		'has_custom' => false,
  		'has_upload' => false
	);

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

	public function vid_division()
	{
		return $this->belongsTo('Vid_division')->orderBy('display_order', 'asc');
	}

	public function school()
	{
		return $this->hasOne('Schools', 'school_id', 'school_id');
	}

	public function student_count()
	{
		return count(explode("\n",trim($this->students)));
	}

	public function student_list()
	{
		return preg_split("/\s*,\s*/", trim($this->students));
	}
}
