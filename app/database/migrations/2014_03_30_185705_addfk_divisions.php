<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddfkDivisions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('divisions', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->foreign('competition_id')->references('id')->on('competitions')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('divisions', function(Blueprint $table)
		{
			$table->dropForeign('divisions_competition_id_foreign');
		});
	}

}
