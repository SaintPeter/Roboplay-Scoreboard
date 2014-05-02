<?php

class Video extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'yt_code' => array('required','regex:#([A-Za-z0-9_-]{5,11})#'),
		'students' => 'required',
		'school_id' => 'required',
		'vid_division_id' => 'required'
	);

	protected $attributes = array(
  		'has_custom' => false
	);

	public function setYtCodeAttribute($code)
	{
		if(preg_match('#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#', $code, $matches)) {
			if(isset($matches[2]) && $matches[2] != ''){
				$this->attributes['yt_code'] = $matches[2];
				return;
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
}
