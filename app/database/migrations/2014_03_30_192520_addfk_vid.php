<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddfkVid extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vid_divisions', function(Blueprint $table) {
			$table->foreign('competition_id')->references('id')->on('vid_competitions')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('vid_divisions', function(Blueprint $table) {
			$table->foreign('vid_divisions_competition_id_foreign');
		});
	}

}
