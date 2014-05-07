<?php

class Vid_division extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'description' => 'required',
		'display_order' => 'required',
		'competition_id' => 'required'
	);

	public function competition() {
		return $this->belongsTo('Vid_competition');
	}

	public function scores()
	{
		return $this->hasMany('Video_scores');
	}

	public function videos()
	{
		return $this->hasMany('Videos');
	}

	/**
	 * Return a list of id => long name keypairs
	 *
	 * @return array
	 */
	public static function longname_array()
	{
		$divlist = Vid_division::with('competition')->get();
		$namelist[0] = "-- Select Video Division --";
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
