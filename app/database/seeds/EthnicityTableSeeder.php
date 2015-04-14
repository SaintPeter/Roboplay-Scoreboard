<?php

class EthnicityTableSeeder extends Seeder {

	public function run()
	{
		Ethnicity::truncate(); // Clear the table before seeding to prevent duplicates
		Ethnicity::create( [ 'name' => 'American Indian or Alaskan Native'] );
		Ethnicity::create( [ 'name' => 'Asian'] );
		Ethnicity::create( [ 'name' => 'Hispanic or Latino'] );
		Ethnicity::create( [ 'name' => 'Black or African-American'] );
		Ethnicity::create( [ 'name' => 'Native Hawaiian or Other Pacific Islander'] );
		Ethnicity::create( [ 'name' => 'White'] );
		Ethnicity::create( [ 'name' => 'Other'] );
	}

}