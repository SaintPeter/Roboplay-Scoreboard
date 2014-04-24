<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// $this->call('UserTableSeeder');
		$this->call('CompetitionsTableSeeder');
		$this->call('DivisionsTableSeeder');
		$this->call('ChallengesTableSeeder');
		$this->call('Vid_competitionsTableSeeder');
		$this->call('Vid_divisionsTableSeeder');
		$this->call('TeamsTableSeeder');
		$this->call('Score_elementsTableSeeder');
		$this->call('Score_runsTableSeeder');
		$this->call('JudgesTableSeeder');
		$this->call('VideosTableSeeder');
	}

}