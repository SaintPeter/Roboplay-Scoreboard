<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddfkScoreElements extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('score_elements', function(Blueprint $table) {
			$table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('score_elements', function(Blueprint $table) {
			$table->dropForeign('score_elements_challenge_id_foreign');
		});
	}

}
