<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddInternalNameInMathChallengesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('math_challenges', function(Blueprint $table)
		{
			$table->renameColumn('internal_name', 'file_name');
		});
		DB::statement("ALTER TABLE math_challenges MODIFY COLUMN file_name VARCHAR(255) AFTER display_name");
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('math_challenges', function(Blueprint $table)
		{
			$table->renameColumn('file_name', 'internal_name');
			DB::statement("ALTER TABLE math_challenges MODIFY COLUMN internal_name VARCHAR(255) AFTER id");
		});
	}

}
