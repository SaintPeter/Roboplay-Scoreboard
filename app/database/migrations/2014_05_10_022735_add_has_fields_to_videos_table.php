<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddHasFieldsToVideosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('videos', function(Blueprint $table)
		{
			$table->dropColumn('has_upload');
			$table->boolean('has_code')->before('has_custom');
			$table->boolean('has_vid')->before('has_custom');

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
			$table->dropColumn('has_vid');
			$table->dropColumn('has_code');
			$table->boolean('has_upload')->after('has_custom');
		});
	}

}
