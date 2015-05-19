<?php

class Division extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'description' => 'required',
		'display_order' => 'required',
		'competition_id' => 'required'
	);

	// Relationships
	public function competition()
	{
		return $this->belongsTo('Competition')->orderBy('name');
	}

	public function challenges()
	{
		return $this->belongsToMany('Challenge')->withPivot('display_order')->orderBy('display_order', 'asc');
	}

	public function teams()
	{
		return $this->hasMany('Team');
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
		$divlist = Division::with('competition')->get();
		$namelist[0] = "-- Select Division --";
		foreach($divlist as $div) {
			$namelist[$div->competition->name][$div->id] = $div->competition->location . " - " . $div->name;
		};
		return $namelist;
	}

	public static function longname_array_counts()
	{
		$divlist = Division::with('competition', 'challenges')->get();
		$namelist[0] = "-- Select Division --";
		foreach($divlist as $div) {
			$namelist[$div->competition->name][$div->id] = $div->competition->location . " - " . $div->name . " ({$div->challenges->count()})";
		};
		return $namelist;
	}

	public function longname()
	{
		return $this->competition->name . ' - ' . $this->name;
	}
}
