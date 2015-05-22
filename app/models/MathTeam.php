<?php

class MathTeam extends \Eloquent {
	protected $fillable = ['name','division_id','school_id','teacher_id','year'];

	public static $rules = array(
		'name' => 'required',
		'division_id' => 'required',
		'school_id' => 'required'
	);

	public static function boot() {
		parent::boot();

		// Detach Students
		static::deleting(function($mathteam) {
			$mathteam->students()->sync([]);
		});
	}

	// Relationships
	public function division()
	{
		return $this->belongsTo('MathDivision');
	}

	public function scores()
	{
		return $this->belongsTo('MathScore');
	}

	public function school()
	{
		return $this->hasOne('Schools', 'school_id', 'school_id');
	}

	public function students() {
		return $this->morphToMany('Student', 'studentable');
	}

	public function teacher() {
		return $this->belongsTo('Wp_user', 'teacher_id', 'ID');
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

		if(count($this->students) > 0) {
			foreach($this->students as $student) {
				$student_list[] = $student->fullName();
			}
		} else {
			$student_list = [ 'No Students' ];
		}

		return $student_list;
	}

}