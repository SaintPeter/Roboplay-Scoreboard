<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddActiveAndFreezeToCompetitionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('competitions', function(Blueprint $table)
		{
			$table->time('freeze_time')->after('event_date');
			$table->boolean('active')->default(true)->after('freeze_time');
			$table->boolean('frozen')->default(false)->after('freeze_time');

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('competitions', function(Blueprint $table)
		{
			$table->dropColumn('active');
			$table->dropColumn('frozen');
			$table->dropColumn('freeze_time');
		});
	}

}
