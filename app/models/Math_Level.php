<?php

class Math_Level extends \Eloquent {
    protected $table = "math_level";
	protected $fillable = [];
	public $timestamps = false;

	static $level_cache;

	public static function getList() {
	    if(!isset($level_cache)) {
    	    $levels = Math_Level::all();
    	    $output = [];

    	    foreach($levels as $level) {
                if($level->parent == 0) {
                    if($level->id == 0) {
                        $output[0] = $level->name;
                    } else {
                        $output[$level->name] = [] ;
                    }
                } else {
                    $parent = $levels->find($level->parent);
                    $output[$parent->name][$level->id] = $level->name;
                }
    	    }
    	    $level_cache = $output;
    	    return $output;
    	} else {
    	    return $level_cache;
    	}
	}

	// Relationships
	public function students() {
		return $this->hasMany('Student');
	}

}