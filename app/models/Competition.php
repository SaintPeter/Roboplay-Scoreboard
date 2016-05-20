<?php

use \Carbon\Carbon;

class Competition extends Eloquent {
	protected $guarded = array();

	public static $rules = array(
		'name' => 'required',
		'description' => 'required',
		'location' => 'required',
		'address' => 'required',
		'event_date' => 'required',
		'color' => 'required'
	);

	public function getDates()
    {
        return [ 'created_at', 'updated_at', 'event_date' ];
    }



	public function divisions()
	{
		return $this->hasMany('Division')->orderBy('display_order', 'asc');
	}

	public function comp_year() {
		return $this->morphToMany('CompYear', 'yearable');
	}

	public function getFreezeTimeAttribute($value)
	{
		return Carbon::parse($value)->format('g:i A');
	}

	public function setFreezeTimeAttribute($value)
	{
		$this->attributes['freeze_time'] = Carbon::parse($value)->format('H:i');
	}

	// Checks to see if it is after the date of the competition
	public function isDone()
	{
	    $today = Carbon::now()->setTimezone('America/Los_Angeles')->startOfDay();
	    return $today->gte($this->event_date->endOfDay());
	}

}
