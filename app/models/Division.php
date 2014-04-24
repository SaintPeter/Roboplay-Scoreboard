<?php

class Division extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'description' => 'required',
		'display_order' => 'required',
		'competition_id' => 'required'
	);

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


	/**
	 * Return a list of id => long name keypairs
	 *
	 * @return array
	 */
	public static function longname_array()
	{
		$divlist = Division::with('Competition')->get();
		$namelist = array();
		foreach($divlist as $div) {
			$namelist[$div->id] = $div->longname();
		};
		return $namelist;
	}

	public function longname()
	{
		return $this->competition->name . ' - ' . $this->name;
	}
}
