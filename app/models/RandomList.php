<?php

class RandomList extends \Eloquent {
	protected $fillable = [
	    'name', 'format', 'popup_format',
	    'd1_format', 'd2_format', 'd3_format', 'd4_format', 'd5_format',
	    'display_order',
	    'challenge_id'];

    public static $rules = [
		'name' => 'required',
		'format' => 'required',
	    'popup_format' => 'required',
		'display_order' => 'numeric'
	];

	public static $chosen = null;

	// We will ALWAYS load elements
	protected $with = [ 'elements' ];

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

	public function get_formatted() {
	    $element_list = $this->elements;

        if(isset($this->chosen)) {
            $choose = $this->chosen;
        } else {
            $this->chosen = $choose = rand(0, count($element_list) - 1);
        }

        $formatted = $this->format_elements($element_list[$choose]);

	    return str_ireplace(array_keys($formatted), $formatted, $this->format);
	}

	public function get_formatted_popup() {
	    $element_list = $this->elements;

        if(isset($this->chosen)) {
            $choose = $this->chosen;
        } else {
            $this->chosen = $choose = rand(0, count($element_list) - 1);
        }

        $formatted = $this->format_elements($element_list[$choose]);

	    return str_ireplace(array_keys($formatted), $formatted, $this->popup_format);

	}

    public function format_elements($elements) {
        $names = [ 'd1', 'd2', 'd3', 'd4', 'd5' ];
        $formats = [ 'd1_format', 'd2_format', 'd3_format', 'd4_format', 'd5_format' ];
        $output = [];

        for($i = 0; $i < 5; $i++) {
            if($this->{$formats[$i]}) {
                $output['{' . $names[$i] . '}'] = sprintf($this->{$formats[$i]}, $elements->{$names[$i]});
            } else {
                break;
            }
        }
        return $output;
    }


	// Relationships
	public function elements()
	{
	    return $this->hasMany('RandomListElement');
	}

}