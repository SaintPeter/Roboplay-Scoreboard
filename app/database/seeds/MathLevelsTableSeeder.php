<?php

class MathLevelsTableSeeder extends Seeder {

	public function run()
	{
		Math_Level::truncate(); // Clear the table before seeding to prevent duplicates
		Math_Level::create( [ 'id' => 0,  'name' => '- Select Math Level -', 'parent' => 0, 'level' => 0 ] );
		Math_Level::create( [ 'id' => 1,  'name' => 'Middle School',         'parent' => 0, 'level' => 0 ] );
		Math_Level::create( [ 'id' => 2,  'name' => 'High School',           'parent' => 0, 'level' => 0 ] );
		Math_Level::create( [ 'id' => 3,  'name' => 'Integrated Math 6',     'parent' => 1, 'level' => 1 ] );
        Math_Level::create( [ 'id' => 4,  'name' => 'Integrated Math 7',     'parent' => 1, 'level' => 1 ] );
        Math_Level::create( [ 'id' => 5,  'name' => 'Integrated Math 8',     'parent' => 1, 'level' => 1 ] );
        Math_Level::create( [ 'id' => 6,  'name' => 'Pre-Algebra (MS)',      'parent' => 1, 'level' => 1 ] );
        Math_Level::create( [ 'id' => 7,  'name' => 'Algebra 1 (MS)',        'parent' => 1, 'level' => 1 ] );
        Math_Level::create( [ 'id' => 8,  'name' => 'Geometry (MS)',         'parent' => 1, 'level' => 1 ] );
        Math_Level::create( [ 'id' => 9,  'name' => 'Integrated Math 1 (MS)','parent' => 1, 'level' => 1 ] );
        Math_Level::create( [ 'id' => 10, 'name' => 'Integrated Math 2 (MS)','parent' => 1, 'level' => 1 ] );
        Math_Level::create( [ 'id' => 11, 'name' => 'Algebra 1 (HS)',        'parent' => 2, 'level' => 2 ] );
        Math_Level::create( [ 'id' => 12, 'name' => 'Geometry (HS)',         'parent' => 2, 'level' => 2 ] );
        Math_Level::create( [ 'id' => 13, 'name' => 'Algebra 2',             'parent' => 2, 'level' => 3 ] );
        Math_Level::create( [ 'id' => 14, 'name' => 'Trigonometry',          'parent' => 2, 'level' => 3 ] );
        Math_Level::create( [ 'id' => 15, 'name' => 'Pre-Calculus',          'parent' => 2, 'level' => 3 ] );
        Math_Level::create( [ 'id' => 16, 'name' => 'Calculus',              'parent' => 2, 'level' => 3 ] );
        Math_Level::create( [ 'id' => 17, 'name' => 'Statistics',            'parent' => 2, 'level' => 3 ] );
        Math_Level::create( [ 'id' => 18, 'name' => 'Integrated Math 1 (HS)','parent' => 2, 'level' => 2 ] );
        Math_Level::create( [ 'id' => 19, 'name' => 'Integrated Math 2 (HS)','parent' => 2, 'level' => 2 ] );
        Math_Level::create( [ 'id' => 20, 'name' => 'Integrated Math 3',     'parent' => 2, 'level' => 3 ] );
	}

}