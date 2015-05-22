<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMathDivisionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('math_divisions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('display_order');
			$table->integer('competition_id')->unsigned();
			$table->foreign('competition_id')->references('id')->on('math_competitions')->onDelete('cascade');
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
		Schema::drop('math_divisions');
	}

}
