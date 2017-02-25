<?php

class Frm_items extends Eloquent {
	public $connection = 'mysql-wordpress';
	protected $table = 'frm_items';
	protected $primaryKey = 'id';
	protected $guarded = ['id'];
	public $timestamps = false;

    // This is a list of unique invoices

    public function getDescriptionAttribute() {
        return unserialize($this->attributes['description']);
    }

    // It has a bunch of fields associated with it
	public function fields() {
		return $this->hasMany('Frm_fields', 'form_id', 'form_id');
	}

    // It has a bunch of item_metas
	public function values() {
	    return $this->hasMany('Frm_item_metas', 'item_id', 'id');
	}
}