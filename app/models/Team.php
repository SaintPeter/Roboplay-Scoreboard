<?php

class Team extends Eloquent {
	protected $guarded = array();
	protected $with = [ 'school', 'school.district', 'school.district.county', 'division', 'division.competition' ];

	public static $rules = array(
		'name' => 'required',
		'division_id' => 'required',
		'school_id' => 'required',
		'students' => 'required'
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
		if(isset($this->school)) {
			return $this->name . ' (' . $this->school->name . ')';
		} else {
			return $this->name . ' (Unknown School)';
		}
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
