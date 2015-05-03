<?php

class Random extends \Eloquent {
	protected $guarded = [ 'id' ];

	public static $rules = [
		'name' => 'required',
		'type' => 'required',
		'format' => 'required',
		'min1' => 'required|numeric',
		'max1' => 'required|numeric',
		'min2' => 'numeric',
		'max2' => 'numeric',
		'display_order' => 'numeric'
	];

	// For filling in the types dropdown
	public static $types = [
		'single' => 'Single',
		'dual' => 'Dual Numbers' ];

	// Create the Random Number output
	public function formatted() {
		switch ($this->type) {
			case 'single':
			dd($this->format);
				return sprintf($this->format, mt_rand($this->min1, $this->max1));
				break;
			case 'dual':
				if($this->may_not_match) {
					$rand1 = mt_rand($this->min1, $this->max1);
					$rand2 = mt_rand($this->min2, $this->max2);
					while($rand1 == $rand2) {
						$rand2 = mt_rand($this->min2, $this->max2);
					}
					return sprintf($this->format, $rand1, $rand2);
				} else {
					return sprintf($this->format, mt_rand($this->min1, $this->max1), mt_rand($this->min2, $this->max2));
				}
				break;
		}

	}

}