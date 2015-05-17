<?php

class CompYear extends \Eloquent {
	// Add your validation rules here
	public static $rules = [
		'year' => 'required|numeric|digits:4'
	];

	// Don't forget to fill this array
	protected $fillable = ['year'];

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
		return $this->morphedByMany('Math_compeitions', 'yearable');
	}

	public function math_divisions() {
		return $this->morphedByMany('Math_divisions', 'yearable');
	}

	// Get the current Comp Year
	public static function current() {
		return CompYear::where('year', Carbon\Carbon::now()->year)->get();
	}

}