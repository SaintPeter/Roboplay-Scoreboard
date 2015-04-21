<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBooleansToVideosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('videos', function(Blueprint $table)
		{
			$table->boolean('has_task')->default('0')->after('has_code');
			$table->boolean('has_choreo')->default('0')->after('has_code');
			$table->boolean('has_story')->default('0')->after('has_code');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('videos', function(Blueprint $table)
		{
			$table->dropColumn('has_task');
			$table->dropColumn('has_choreo');
			$table->dropColumn('has_story');
		});
	}

}
