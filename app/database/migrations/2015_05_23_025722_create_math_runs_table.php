<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMathRunsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('math_runs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('run');
			$table->time('run_time');
			$table->integer('score');
			$table->integer('judge_id')->unsigned();
			$table->integer('team_id')->unsigned();
			$table->integer('challenge_id')->unsigned();
			$table->integer('division_id')->unsigned();
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('math_runs');
	}

}
