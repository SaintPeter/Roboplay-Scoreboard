<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddZeroToRubric extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rubric', function(Blueprint $table)
		{
			$table->string('zero')->after('order')->default('');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('rubric', function(Blueprint $table)
		{
			$table->dropColumn('zero');
		});
	}

}
