<?php

class Counties extends Eloquent {
	public $connection = 'mysql-wordpress';
        protected $table = 'sdd_county';
        protected $primaryKey = 'county_id';
        protected $guarded = array('county_id');

        public static $rules = array();

	public function districts() {
		return $this->hasMany('districts');
	}
}
