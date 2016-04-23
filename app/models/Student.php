<?php

class Student extends \Eloquent {
	protected $table = 'students';
	protected $guarded = array('id');

	public static $rules = [
		'first_name' => 'required',
		'last_name' => 'required',
		'ssid' => 'required|numeric|digits_between:1,12',
		'gender' => 'required|not_in:0',
		'ethnicity_id' => 'required|exists:ethnicities,id',
		'math_level_id' => 'required|exists:math_level,id|not_in:0',
		'grade' => 'required|numeric|min:5|max:14',
		'email' => 'sometimes|email'
	];

	// Relationships
	public function ethnicity() {
		return $this->belongsTo('Ethnicity');
	}

	public function math_level() {
		return $this->belongsTo('Math_Level');
	}

	public function teacher() {
		return $this->belongsTo('Wp_user', 'teacher_id', 'ID');
	}

	public function school() {
		return $this->hasOne('Schools', 'school_id', 'school_id');
	}

	public function teams() {
		return $this->morphedByMany('Team', 'studentable');
	}

	public function videos() {
		return $this->morphedByMany('Video', 'studentable');
	}

	public function maths() {
		return $this->morphedByMany('MathTeam', 'studentable');
	}

	public function fullName() {
		$name = $this->first_name  . " ";
		$name .= (empty($this->middle_name) ? '' : ($this->nickname ? '"' . $this->middle_name . '" ' : $this->middle_name . ' '));
		$name .= $this->last_name;
		return $name;
	}

}