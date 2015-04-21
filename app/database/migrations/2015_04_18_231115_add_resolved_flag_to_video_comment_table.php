<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddResolvedFlagToVideoCommentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_comment', function(Blueprint $table)
		{
			$table->text('resolution')->default('')->after('comment');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('video_comment', function(Blueprint $table)
		{
			$table->dropColumn('resolution');
		});
	}

}
