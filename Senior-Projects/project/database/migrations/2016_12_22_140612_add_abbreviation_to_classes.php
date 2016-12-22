<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAbbreviationToClasses extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table( 'classes', function ( Blueprint $table ) {
			$table->string( "abbreviation" )->after( 'class' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table( 'classes', function ( Blueprint $table ) {
			$table->dropColumn( [ "abbreviation" ] );
		} );
	}
}
