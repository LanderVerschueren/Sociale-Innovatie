<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClassesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create( 'classes', function ( Blueprint $table ) {
			$table->increments( 'id' );
			$table->integer( 'year' )->unsigned();
			$table->string( 'class' );
			$table->timestamps();
		} );

		Schema::table( 'class_groups', function ( Blueprint $table ) {
			$table->dropColumn( [ 'year', 'class' ] );
			$table->integer( 'class_id' )->unsigned()->after( 'id' );
			$table->string( 'class_group' )->after( 'class_id' );
			$table->foreign( 'class_id' )
			      ->references( 'id' )
			      ->on( 'classes' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table( 'class_groups', function ( Blueprint $table ) {
			$table->dropForeign( [ 'class_id' ] );
			$table->dropColumn( [ 'class_id', 'class_group' ] );
			$table->integer( 'year' )->after( 'id' );
			$table->string( 'class' )->after( 'year' );
		} );
		Schema::dropIfExists( 'classes' );
	}
}
