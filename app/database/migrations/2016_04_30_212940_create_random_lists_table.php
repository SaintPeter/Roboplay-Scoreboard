<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRandomListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('random_lists', function(Blueprint $table)
		{
		    $table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('name');
			$table->string('format');
			$table->string('popup_format');
			$table->string('d1_format');
			$table->string('d2_format');
			$table->string('d3_format');
			$table->string('d4_format');
			$table->string('d5_format');
			$table->integer('display_order')->default(1);
			$table->integer('challenge_id')->unsigned();
			$table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
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
		Schema::drop('random_lists');
	}

}
