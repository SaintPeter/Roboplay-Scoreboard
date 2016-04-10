<?php

class CompYear extends \Eloquent {
	// Add your validation rules here
	public static $rules = [
		'year' => 'required|numeric|digits:4'
	];

	// Don't forget to fill this array
	protected $fillable = ['year', 'invoice_type', 'invoice_type_id'];

	// Get the year requested or the most recent
	public static function yearOrMostRecent($year) {
	    if($year > 0 AND CompYear::where('year', $year)->count() > 0) {
	        return $year;
	    } else {
	        return CompYear::orderBy('year', 'desc')->first()->year;
	    }
	}

	// Get the current Comp Year
	public static function current() {
		return CompYear::orderBy('year', 'desc')->first();
	}

	// Relationships
	public function competitions() {
		return $this->morphedByMany('Competition', 'yearable');
	}

	public function divisions() {
		return $this->morphedByMany('Division', 'yearable');
	}

	public function vid_competitions() {
		return $this->morphedByMany('Vid_competition', 'yearable');
	}

	public function vid_divisions() {
		return $this->morphedByMany('Vid_division', 'yearable');
	}

	public function math_competitions() {
		return $this->morphedByMany('MathCompetition', 'yearable');
	}

	public function math_divisions() {
		return $this->morphedByMany('MathDivision', 'yearable');
	}
}