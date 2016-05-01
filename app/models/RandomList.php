<?php

class RandomList extends \Eloquent {
	protected $fillable = [
	    'name', 'format', 'popup_format',
	    'd1', 'd2', 'd3', 'd4', 'd5',
	    'display_order',
	    'challenge_id'];

    public static $rules = [
		'name' => 'required',
		'format' => 'required',
	    'popup_format' => 'required',
		'display_order' => 'numeric'
	];

	public function get_elements() {
	    $element_list = $this->elements;

        if(count($element_list)) {
            $output = [];

    	    foreach($element_list as $element) {
    	        $output[] = join(';', [ $element->d1, $element->d2, $element->d3, $element->d4, $element->d5 ]);
    	    }

    	    return join("\n", $output);
    	} else {
    	    return '';
    	}
	}


	// Relationships
	public function elements()
	{
	    return $this->hasMany('RandomListElement');
	}

}