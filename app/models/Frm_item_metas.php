<?php

class Frm_item_metas extends Eloquent {
	public $connection = 'mysql-wordpress';
	protected $table = 'frm_item_metas';
	protected $primaryKey = 'id';
	protected $guarded = array('id');
	public $timestamps = false;

	public $data = [];

}