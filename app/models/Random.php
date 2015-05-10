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

	// Store Random Numbers for this page display
	private static $rand1 = null;
	private static $rand2 = null;

	// Create the Random Number output
	public function formatted() {
		// If the numbers have not been created, create them
		if($this->rand1 == null) {
				$this->rand1 = mt_rand($this->min1, $this->max1);
				$this->rand2 = mt_rand($this->min2, $this->max2);

			if($this->may_not_match) {
				while($this->rand1 == $this->rand2) {
					$this->rand2 = mt_rand($this->min2, $this->max2);
				}
			}
		}
		switch ($this->type) {
			case 'single':
				return sprintf($this->format, $this->rand1);
				break;
			case 'dual':
				return sprintf($this->format, $this->rand1, $this->rand2);
				break;
		}

	}

}