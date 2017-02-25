<?php

class Frm_fields extends Eloquent {
	public $connection = 'mysql-wordpress';
	protected $table = 'frm_fields';
	protected $primaryKey = 'id';
	protected $guarded = array('id');
	public $timestamps = false;

	public static $rules = array();

    // Attributes
    public function getFieldOptionsAttribute() {
        return unserialize($this->attributes['field_options']);
    }

}
