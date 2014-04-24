<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddfkScoreRuns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('score_runs', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('score_runs', function(Blueprint $table)
		{
			$table->dropForeign('score_runs_division_id_foreign');
		});
	}

}
