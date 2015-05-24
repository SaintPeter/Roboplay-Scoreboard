<?php

class MathDivision extends \Eloquent {
protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'display_order' => 'required|numeric',
		'competition_id' => 'required'
	);

	// Relationships
	public function competition() {
		return $this->belongsTo('MathCompetition');
	}

	public function teams() {
		return $this->hasMany('MathTeam', 'division_id');
	}

	public function scores()
	{
		return $this->hasMany('MathRun');
	}

	public function challenges()
	{
		return $this->hasMany('MathChallenge', 'division_id')->orderBy('order');
	}

	public function comp_year() {
		return $this->morphToMany('CompYear', 'yearable');
	}

		/**
	 * Return a list of id => long name keypairs
	 *
	 * @return array
	 */
	public static function longname_array()
	{
		$divlist = MathDivision::with('competition')->get();
		$namelist[0] = "-- Select Math Division --";
		foreach($divlist as $div) {
			$namelist[$div->competition->name][$div->id] = $div->name;
		};
		return $namelist;
	}

	public function longname()
	{
		return $this->competition->name . ' - ' . $this->name;
	}

}