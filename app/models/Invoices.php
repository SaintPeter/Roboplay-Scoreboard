<?php

class Invoices extends \Eloquent {
	protected $fillable = ['remote_id', 'user_id', 'year'];

	// Relationships
	public function user() {
		return $this->belongsTo('Wp_user', 'user_id', 'ID');
	}

	public function judge() {
		return $this->belongsTo('Judge', 'user_id', 'id' );
	}

	public function videos() {
		return $this->hasMany('Video', 'user_id', 'user_id');
	}

	public function teams() {
		return $this->hasMany('Team', 'user_id', 'user_id');
	}

}