<?php

use Carbon\Carbon;

class MathRun extends \Eloquent {
	protected $fillable = [
		'run',
		'run_time',
		'score',
		'judge_id',
		'team_id',
		'challenge_id',
		'division_id'
		];

	protected $softDelete = true;

	public static $rules = array(
		'run' => 'required',
		'run_time' => 'required',
		'scores' => 'required',
		'total' => 'required',
		'judge_id' => 'required',
		'team_id' => 'required',
		'challenge_id' => 'required',
		'division_id' => 'required'
	);

	// Mutators and Accessors
	public function getRunTimeAttribute($value) {
		if(isset($value)) {
			// Get time from this event return as a time string
			return Carbon::parse($value)->format('g:i a');
		} else {
			return "Time Error";
		}
	}

	// Relationships
	public function team()
	{
		return $this->belongsTo('MathTeam','team_id');
	}

	public function challenge()
	{
		return $this->belongsTo('MathChallenge','challenge_id');
	}

	public function division()
	{
		return $this->belongsTo('MathDivision','division_id');
	}

	public function judge()
	{
		return $this->belongsTo('Judge');
	}
}