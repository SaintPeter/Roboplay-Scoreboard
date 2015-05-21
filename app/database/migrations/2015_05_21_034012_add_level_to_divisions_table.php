<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddLevelToDivisionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('divisions', function(Blueprint $table)
		{
			$table->integer('level')->after('display_order');
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
			$table->dropColumn('level');
		});
	}

}
