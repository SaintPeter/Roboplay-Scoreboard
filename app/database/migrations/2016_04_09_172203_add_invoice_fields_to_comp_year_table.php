<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddInvoiceFieldsToCompYearTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('comp_years', function(Blueprint $table)
		{
			$table->integer('invoice_type')->after('year');
			$table->integer('invoice_type_id')->after('invoice_type');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('comp_year', function(Blueprint $table)
		{
			$table->dropColumn('invoice_type');
			$table->dropColumn('invoice_type_id');
		});
	}

}
