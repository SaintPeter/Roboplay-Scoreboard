<?php

class Schools extends Eloquent {
	public $connection = 'mysql-wordpress';
	protected $table = 'sdd_school';
	protected $primaryKey = 'school_id';
	protected $guarded = array('school_id');

	public static $rules = array();


}
