<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddScoreGroupToVideoScoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_scores', function(Blueprint $table)
		{
			$table->integer('score_group')->after('judge_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('video_scores', function(Blueprint $table)
		{
			$table->dropColumn('score_group');
		});
	}

}
