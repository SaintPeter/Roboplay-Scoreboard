<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRubricTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rubric', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('vid_score_type_id')->unsigned();
			$table->string('element');
			$table->string('element_name');
			$table->integer('order');
			$table->text('one');
			$table->text('two');
			$table->text('three');
			$table->text('four');
			$table->integer('vid_competition_id')->unsigned();
			$table->foreign('vid_competition_id')->references('id')->on('vid_competitions')->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('rubric');
	}

}
