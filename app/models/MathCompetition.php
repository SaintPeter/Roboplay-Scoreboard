<?php

class MathCompetition extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $guarded = ['id'];

	public function divisions() {
		return $this->hasMany('MathDivision', 'competition_id', 'id');
	}

	public function comp_year() {
		return $this->morphToMany('CompYear', 'yearable');
	}

}