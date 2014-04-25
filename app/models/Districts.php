<?php

class Districts extends Eloquent {
public $connection = 'mysql-wordpress';
        protected $table = 'sdd_district';
        protected $primaryKey = 'district_id';
        protected $guarded = array('district_id');

        public static $rules = array();

	public function county() {
		return $this->belongsTo('Counties','county_id', 'county_id');

	}

	public function schools() {
		return $this->hasMany('Schools', 'school_id', 'school_id');
	}
}
