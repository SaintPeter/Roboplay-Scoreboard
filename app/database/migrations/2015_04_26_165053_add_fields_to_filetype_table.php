<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFieldsToFiletypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('filetype', function(Blueprint $table)
		{
			$table->string('viewer')->default('');
			$table->string('icon')->default('fa-file-o');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('filetype', function(Blueprint $table)
		{
			$table->dropColumn('viewer');
			$table->dropColumn('icon');
		});
	}

}
