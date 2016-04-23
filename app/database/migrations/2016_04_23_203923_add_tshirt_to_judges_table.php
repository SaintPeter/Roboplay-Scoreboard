<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddTshirtToJudgesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('judges', function(Blueprint $table)
		{
			$table->string('tshirt', 3)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('judges', function(Blueprint $table)
		{
			$table->dropColumn('tshirt');
		});
	}

}
