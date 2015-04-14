<?php

class Wp_invoice_table extends Eloquent {
	public $connection = 'mysql-wordpress';
	protected $table = 'invoice_table';
	protected $primaryKey = 'invoice_no';
	protected $guarded = array('invoice_no');
	public $timestamps = false;

	private $data = [];

	public function invoice_data() {
		return $this->hasMany('Wp_invoice_data', 'invoice_no', 'invoice_no');
	}

	public function user() {
		return $this->belongsTo('Wp_user', 'user_id', 'ID');
	}

	public function judge() {
		return $this->belongsTo('Judge', 'user_id', 'id' );
	}

	public function videos() {
		return $this->hasMany('Video', 'user_id', 'teacher_id');
	}

	public function teams() {
		return $this->hasMany('Team', 'user_id', 'teacher_id');
	}

	public function getData($key, $default = '') {
		if(empty($this->data)) {
			$this->data = $this->invoice_data->lists('field_value', 'field_name');
		}
		if(array_key_exists($key, $this->data)) {
			return $this->data[$key];
		} else {
			return $default;
		}
	}



}