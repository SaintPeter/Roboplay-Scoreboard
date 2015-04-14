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
	}

}