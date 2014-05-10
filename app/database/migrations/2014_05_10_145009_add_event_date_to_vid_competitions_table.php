<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddEventDateToVidCompetitionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vid_competitions', function(Blueprint $table)
		{
			$table->renameColumn('event_date','event_start');
			$table->date('event_end')->after('event_date');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('vid_competitions', function(Blueprint $table)
		{
			$table->renameColumn('event_start','event_date');
			$table->dropColumn('event_end');
		});
	}

}
