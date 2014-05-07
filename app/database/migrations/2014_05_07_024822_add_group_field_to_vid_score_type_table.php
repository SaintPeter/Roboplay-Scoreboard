<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddGroupFieldToVidScoreTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('vid_score_types', function(Blueprint $table)
		{
			$table->integer('group');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('vid_score_types', function(Blueprint $table)
		{
			$table->dropColumn('group');
		});
	}

}
