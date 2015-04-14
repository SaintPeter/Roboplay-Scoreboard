<?php

class Wp_invoice_data extends Eloquent {
	public $connection = 'mysql-wordpress';
	protected $table = 'invoice_data';
	protected $primaryKey = 'row_id';
	protected $guarded = ['row_id'];
	public $timestamps = false;

	public function invoice() {
			return $this->belongsTo('Wp_invoice_table', 'invoice_no', 'invoice_no');
	}
}