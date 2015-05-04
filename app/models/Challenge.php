<?php

class Challenge extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'internal_name' => 'required',
		'display_name' => 'required',
		'rules' => 'required'
	);

	public static $levels = [
		0 => '- Select Level - ',
		1 => 1,
		2 => 2,
		3 => 3
	];

	public function score_elements()
	{
		return $this->hasMany('Score_element')->orderBy('element_number', 'asc');
	}

	public function randoms()
	{
		return $this->hasMany('Random')->orderBy('display_order', 'asc');
	}

	public function divisions()
	{
		return $this->belongsToMany('Division')->withPivot('display_order');
	}

	public function run_count($team_id)
	{
		return Score_run::where('team_id', $team_id)->where('challenge_id', $this->id)->count();
	}

	public function runs($team_id)
	{
		return Score_run::where('team_id', $team_id)->where('challenge_id', $this->id)->orderBy('run_number', 'asc')->get();
	}

	public function scores()
	{
		return $this->hasMany('Score_run')->orderBy('run_number', 'asc');
	}
}
