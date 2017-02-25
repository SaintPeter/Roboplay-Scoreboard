<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddExtraToVideoScores extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_scores', function(Blueprint $table)
		{
			$table->integer('s6')->after('s5')->default('0');
			$table->integer('s7')->after('s6')->default('0');
			$table->integer('s8')->after('s7')->default('0');
			$table->integer('s9')->after('s8')->default('0');
			$table->integer('s10')->after('s9')->default('0');
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
			$table->dropColumn('s6');
			$table->dropColumn('s7');
			$table->dropColumn('s8');
			$table->dropColumn('s9');
			$table->dropColumn('s10');
		});
	}

}
