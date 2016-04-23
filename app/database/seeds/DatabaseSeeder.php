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
		$this->call('FiletypeTableSeeder');
		$this->call('Vid_score_typeTableSeeder');
		$this->call('EthnicityTableSeeder');
		$this->call('MathLevelsTableSeeder');

		// Disable Foreign Key checks for this seeder
	    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
		$this->call('VideoAwardsTableSeeder');
		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	}

}