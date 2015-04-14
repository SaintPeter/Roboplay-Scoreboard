<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('students', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('first_name');
			$table->string('last_name');
			$table->string('middle_name');
			$table->boolean('nickname');
			$table->string('ssid', 12);
			$table->string('gender');
			$table->integer('ethnicity_id')->unsigned();
			$table->integer('grade');
			$table->string('email');
			$table->integer('year');
			$table->integer('teacher_id')->unsigned();
			$table->integer('school_id')->unsigned();
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
		Schema::drop('students');
	}

}
