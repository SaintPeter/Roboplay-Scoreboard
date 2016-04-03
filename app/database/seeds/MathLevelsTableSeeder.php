<?php

class MathLevelsTableSeeder extends Seeder {

	public function run()
	{
		Math_Level::truncate(); // Clear the table before seeding to prevent duplicates
		Math_Level::create( [ 'id' => 0,  'name' => '- Select Math Level -', 'parent' => 0 ] );
		Math_Level::create( [ 'id' => 1,  'name' => 'Middle School',         'parent' => 0 ] );
		Math_Level::create( [ 'id' => 2,  'name' => 'High School',           'parent' => 0 ] );
		Math_Level::create( [ 'id' => 3,  'name' => 'Integrated Math 6',     'parent' => 1 ] );
        Math_Level::create( [ 'id' => 4,  'name' => 'Integrated Math 7',     'parent' => 1 ] );
        Math_Level::create( [ 'id' => 5,  'name' => 'Integrated Math 8',     'parent' => 1 ] );
        Math_Level::create( [ 'id' => 6,  'name' => 'Pre-Algebra',           'parent' => 1 ] );
        Math_Level::create( [ 'id' => 7,  'name' => 'Algebra 1',             'parent' => 2 ] );
        Math_Level::create( [ 'id' => 8,  'name' => 'Geometry',              'parent' => 2 ] );
        Math_Level::create( [ 'id' => 9,  'name' => 'Algebra 2',             'parent' => 2 ] );
        Math_Level::create( [ 'id' => 11, 'name' => 'Trigonometry',          'parent' => 2 ] );
        Math_Level::create( [ 'id' => 12, 'name' => 'Pre-Calculus',          'parent' => 2 ] );
        Math_Level::create( [ 'id' => 13, 'name' => 'Calculus',              'parent' => 2 ] );
        Math_Level::create( [ 'id' => 14, 'name' => 'Statistics',            'parent' => 2 ] );
        Math_Level::create( [ 'id' => 15, 'name' => 'Integrated Math 1',     'parent' => 2 ] );
        Math_Level::create( [ 'id' => 16, 'name' => 'Integrated Math 2',     'parent' => 2 ] );
        Math_Level::create( [ 'id' => 17, 'name' => 'Integrated Math 3',     'parent' => 2 ] );
	}

}