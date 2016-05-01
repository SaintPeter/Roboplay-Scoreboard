<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRandomListElementsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('random_list_elements', function(Blueprint $table)
		{
		    $table->engine = 'InnoDB';
			$table->increments('id');
			$table->integer('random_list_id')->unsigned()->index();
			$table->foreign('random_list_id')->references('id')->on('random_lists')->onDelete('cascade');
			$table->string('d1');
			$table->string('d2');
			$table->string('d3');
			$table->string('d4');
			$table->string('d5');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('random_list_elements');
	}

}
