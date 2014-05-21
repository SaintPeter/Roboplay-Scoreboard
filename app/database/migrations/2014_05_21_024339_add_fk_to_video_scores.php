<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFkToVideoScores extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('video_scores', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->foreign('vid_division_id')->references('id')->on('vid_divisions')->onDelete('cascade');
			$table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('video_scores', function(Blueprint $table)
		{
			$table->dropForeign('video_scores_vid_division_id_foreign');
			$table->dropForeign('video_scores_video_id_foreign');
		});
	}

}
