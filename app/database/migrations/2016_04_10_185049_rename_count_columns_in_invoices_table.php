<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenameCountColumnsInInvoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('invoices', function(Blueprint $table)
		{
			$table->renameColumn('teams', 'team_count');
			$table->renameColumn('videos', 'video_count');
			$table->renameColumn('math', 'math_count');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('invoices', function(Blueprint $table)
		{
			$table->renameColumn('team_count', 'teams');
			$table->renameColumn('video_count', 'videos');
			$table->renameColumn('math_count', 'math');
		});
	}

}
