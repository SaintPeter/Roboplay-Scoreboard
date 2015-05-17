<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSoftDeleteToScoreRunsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('score_runs', function(Blueprint $table)
		{
			$table->string('reason')->after('division_id')->default('');
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('score_runs', function(Blueprint $table)
		{
			$table->drop('reason');
			$table->dropSoftDeletes();
		});
	}

}
