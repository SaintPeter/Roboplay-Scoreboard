<?php

class Schedule extends \Eloquent {
	protected $fillable = [ 'start', 'end', 'display'];
	public $timestamps = false;
	public $table = 'schedule';

	public static $rules = [
	    'start' => 'required|date_format:h:i a',
	    'end' => 'required|date_format:h:i a',
	    'display' => 'required'
	];

	public function getStartAttribute($value) {
	    return Carbon\Carbon::createFromFormat('H:i:s', $value);
	}

	public function setStartAttribute($value) {
	    $this->attributes['start'] = Carbon\Carbon::parse($value)->toTimeString();
	}

	public function getEndAttribute($value) {
	    return Carbon\Carbon::createFromFormat('H:i:s', $value);
	}

	public function setEndAttribute($value) {
	    $this->attributes['end'] = Carbon\Carbon::parse($value)->toTimeString();
	}

}