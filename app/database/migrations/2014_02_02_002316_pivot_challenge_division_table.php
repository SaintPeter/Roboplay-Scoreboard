<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PivotChallengeDivisionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('challenge_division', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('challenge_id')->unsigned()->index();
			$table->integer('division_id')->unsigned()->index();
			$table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
			$table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
			$table->integer('display_order')->unsigned();
		});
	}



	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('challenge_division');
	}

}
