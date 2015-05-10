<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRandomsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('randoms', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('name');
			$table->string('type')->default('single');
			$table->string('format');
			$table->integer('min1')->default(1);
			$table->integer('max1')->default(1);
			$table->integer('min2')->default(1);
			$table->integer('max2')->default(1);
			$table->boolean('may_not_match')->default(false);
			$table->integer('display_order');
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
		Schema::drop('randoms');
	}

}
