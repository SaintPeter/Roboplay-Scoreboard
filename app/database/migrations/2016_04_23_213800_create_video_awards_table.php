<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVideoAwardsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('video_awards', function(Blueprint $table)
		{
		    $table->engine = 'InnoDB';
			$table->integer('id')->unsigned()->index();
			$table->string('name');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('video_awards');
	}

}
