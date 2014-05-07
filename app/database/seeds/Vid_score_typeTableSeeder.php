<?php

class Vid_score_typeTableSeeder extends Seeder {

	public function run()
	{
			Vid_score_type::create(['name' => 'story', 'display_name' => 'Storyline', 'group' => 1 ]);
			Vid_score_type::create(['name' => 'choreo', 'display_name' => 'Choreography', 'group' => 1 ]);
			Vid_score_type::create(['name' => 'task', 'display_name' => 'Interesting Task', 'group' => 1 ]);
			Vid_score_type::create(['name' => 'custom', 'display_name' => 'Custom Designed Part', 'group' => 2]);
			Vid_score_type::create(['name' => 'compute', 'display_name' => 'Computational Thinking', 'group' => 3 ]);
	}

}