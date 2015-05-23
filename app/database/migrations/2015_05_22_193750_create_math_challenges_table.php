<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMathChallengesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('math_challenges', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('order');
			$table->string('internal_name');
			$table->string('display_name');
			$table->text('description');
			$table->integer('points');
			$table->integer('level');
			$table->float('multiplier')->default('1.0');
			$table->integer('year');
			$table->integer('division_id')->unsigned();
			$table->foreign('division_id')->references('id')->on('math_divisions')->onDelete('cascade');
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
		Schema::drop('math_challenges');
	}

}
