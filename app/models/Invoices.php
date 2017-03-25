<?php

class Invoices extends \Eloquent {
	protected $fillable = ['remote_id', 'paid', 'notes', 'user_id', 'year'];

	// Relationships
	public function user() {
		return $this->belongsTo('Judge');
	}

	public function wp_user() {
		return $this->belongsTo('Wp_user', 'user_id', 'ID');
	}

	public function judge() {
		return $this->belongsTo('Judge', 'user_id', 'id' );
	}

	public function school() {
	    return $this->belongsTo('School', 'wp_school_id', 'id');
	}

	public function videos() {
		return $this->hasMany('Video', 'teacher_id', 'user_id');
	}

	public function teams() {
		return $this->hasMany('Team', 'teacher_id', 'user_id');
	}

}