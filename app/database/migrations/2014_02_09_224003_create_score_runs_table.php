<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateScoreRunsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('score_runs', function(Blueprint $table) {
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->integer('run_number');
			$table->time('run_time');
			$table->string('scores');
			$table->integer('total');
			$table->integer('judge_id');
			$table->integer('team_id')->unsigned();
			$table->integer('challenge_id')->unsigned();
			$table->integer('division_id')->unsigned();
			$table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
			$table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('score_runs');
	}

}
