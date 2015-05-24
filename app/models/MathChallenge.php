<?php

class MathChallenge extends \Eloquent {
	protected $fillable = [
		'order',
		'file_name',
		'display_name',
		'description',
		'points',
		'level',
		'multiplier',
		'year',
		'division_id'];

	public static $rules = [
		'order' => 'required',
		'file_name' => 'required',
		'display_name' => 'required',
		'description' => 'required',
		'points' => 'required|integer',
		'level' => 'required|integer',
		'multiplier' => 'required|numeric',
		'year' => 'required|integer',
		'division_id' => 'required|integer'
	];

	public $total;

	// Relationships
	public function division() {
		return $this->belongsTo('MathDivision', 'division_id');
	}

	public function scores() {
		return $this->hasMany('MathRun', 'challenge_id')->orderBy('run', 'asc');
	}

	public function scores_with_trash()
	{
		return $this->hasMany('MathRun', 'challenge_id')->orderBy('run', 'asc')->withTrashed();
	}

	// Utility Functions
	public function run_count($team_id)
	{
		return MathRun::where('team_id', $team_id)->where('challenge_id', $this->id)->count();
	}

	public function runs($team_id)
	{
		return MathRun::where('team_id', $team_id)->where('challenge_id', $this->id)->orderBy('run', 'asc')->get();
	}





}