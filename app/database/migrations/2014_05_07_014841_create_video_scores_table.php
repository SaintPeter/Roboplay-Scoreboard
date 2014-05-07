<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVideoScoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('video_scores', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('vid_score_type_id')->unsigned();
			$table->integer('video_id')->unsigned();
			$table->integer('vid_division_id')->unsigned();
			$table->integer('judge_id')->unsigned();
			$table->integer('s1')->default(0);
			$table->integer('s2')->default(0);
			$table->integer('s3')->default(0);
			$table->integer('s4')->default(0);
			$table->integer('s5')->default(0);
			$table->integer('total');
			$table->float('average');
			$table->float('norm_avg');
			$table->timestamps();
			$table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
			$table->foreign('vid_division_id')->references('id')->on('vid_divisions')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('video_scores');
	}

}
