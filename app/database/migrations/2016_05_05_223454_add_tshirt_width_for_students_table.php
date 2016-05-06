<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTshirtWidthForStudentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('students', function(Blueprint $table)
		{
			DB::update('ALTER TABLE `students` MODIFY `tshirt` VARCHAR(4)');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('students', function(Blueprint $table)
		{
			DB::update('ALTER TABLE `students` MODIFY `tshirt` VARCHAR(2)');
		});
	}

}
