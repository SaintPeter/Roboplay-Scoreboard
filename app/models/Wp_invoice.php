<?php

class Wp_invoice extends Eloquent {
	public $connection = 'mysql-wordpress';
	protected $table = 'CSTEM_Day_Invoice';
	protected $primaryKey = 'invoice_no';
	protected $guarded = array('invoice_no');
	public $timestamps = false;

	public static $rules = array();

	public function user() {
		return $this->belongsTo('Wp_user', 'user_id', 'ID');
	}

	public function judge() {
		return $this->belongsTo('Judge', 'user_id', 'id' );
	}

	public function videos() {
		return $this->hasMany('Video', 'school_id', 'school_id');
	}

	public function school() {
		return $this->belongsTo('Schools', 'school_id', 'school_id');
	}

	public function challenge_division() {
		return $this->hasOne('Division', 'id', 'division_id');
	}

	public function vid_division() {
		return $this->belongsTo('Vid_division', 'vid_division_id', 'id');
	}

	public function getDateAttribute($value) {
		if(isset($value)) {
			// Get time from this event, change it to local time, return as a string
			$dt = new Carbon($value, new DateTimeZone("UTC"));
			$dt->setTimeZone("PST");
			return $dt->format('n/j/Y');
		} else {
			return "Time Error";
		}
	}
}
