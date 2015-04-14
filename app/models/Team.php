<?php

class Team extends Eloquent {
	protected $guarded = array();
	protected $with = [ 'school', 'school.district', 'school.district.county', 'division', 'division.competition' ];

	public static $rules = array(
		'name' => 'required',
		'division_id' => 'required',
		'school_id' => 'required'
	);

	public static function boot() {
		parent::boot();

		// Detach Students
		static::deleting(function($team) {
			$team->students()->sync([]);
		});
	}

	// Relationships
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

	public function students() {
		return $this->morphToMany('Student', 'studentable');
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
		return $this->students()->count();
	}

	public function student_list()
	{
		$student_list = [];

		foreach($this->students as $student) {
			$student_list[] = $student->fullName();
		}

		return $student_list;
	}
}
