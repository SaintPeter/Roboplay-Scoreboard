<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMathTeamsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('math_teams', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('division_id')->unsigned();
			$table->integer('school_id');
			$table->integer('teacher_id');
			$table->integer('year');
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
		Schema::drop('math_teams');
	}

}
