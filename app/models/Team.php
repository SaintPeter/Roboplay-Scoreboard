<?php

class Team extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'division_id' => 'required',
		'school_name' => 'required',
	);

	public function division()
	{
		return $this->belongsTo('Division');
	}

	public function scores()
	{
		return $this->belongsTo('Score_run');
	}
	
	public function school()
	{
		return $this->hasOne('Schools', 'school_id', 'school_id');
	}

	public function longname()
	{
		return $this->name . ' (' . $this->school_name . ')';
	}
}
