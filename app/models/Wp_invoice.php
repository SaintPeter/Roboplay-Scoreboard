<?php

class Wp_invoice extends Eloquent {
	public $connection = 'mysql-wordpress';
	protected $table = 'CSTEM_Day_Invoice';
	protected $primaryKey = 'invoice_no';
	protected $guarded = array('invoice_no');
	public $timestamps = false;

	public static $rules = array();

	public function user() {
		return $this->hasOne('Wp_user', 'ID', 'user_id');
	}

	public function school() {
		return $this->hasOne('Schools', 'school_id', 'school_id');
	}

	public function division() {
		return $this->hasOne('Division');
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
