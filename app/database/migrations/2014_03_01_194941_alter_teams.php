<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTeams extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('teams', function(Blueprint $table)
		{
			$table->dropColumn('school_id');
			$table->dropColumn('owner_id');
			$table->dropColumn('vid_division_id');
			$table->string('school_name')->after('owner_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('teams', function(Blueprint $table)
		{
			$table->integer('school_id');
			$table->integer('vid_division_id');
			$table->integer('owner_id');
			$table->dropColumn('school_name');
		});
	}

}