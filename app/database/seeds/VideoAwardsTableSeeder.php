<?php

class VideoAwardsTableSeeder extends Seeder {

	public function run()
	{
	    VideoAward::truncate();
		VideoAward::create([ 'id' => 1, 'name' => 'Best Story' ]);
		VideoAward::create([ 'id' => 2, 'name' => 'Best Choreography' ]);
		VideoAward::create([ 'id' => 3, 'name' => 'Most Interesting Task' ]);
		VideoAward::create([ 'id' => 4, 'name' => 'Best Custom Part' ]);
		VideoAward::create([ 'id' => 5, 'name' => 'Best Computational Thinking' ]);
		VideoAward::create([ 'id' => 6, 'name' => 'Best Overall' ]);
	}

}