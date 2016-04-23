<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVideoVideoAwardTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('video_video_award', function(Blueprint $table)
		{
		    $table->engine = 'InnoDB';
			$table->increments('id');
			$table->integer('video_id')->unsigned()->index();
			$table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
			$table->integer('video_award_id')->unsigned()->index();
			$table->foreign('video_award_id')->references('id')->on('video_awards')->onDelete('cascade');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('video_video_award');
	}

}
