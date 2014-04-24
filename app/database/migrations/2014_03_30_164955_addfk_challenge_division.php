<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddfkChallengeDivision extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('challenge_division', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
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
		Schema::table('challenge_division', function(Blueprint $table)
		{

		});
	}

}
