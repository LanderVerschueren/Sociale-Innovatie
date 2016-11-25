<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelationsToTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table( 'users', function ( Blueprint $table ) {
			$table->foreign( 'class_group_id' )
			      ->references( 'id' )
			      ->on( 'class_groups' );
		} );
		Schema::table( 'results', function ( Blueprint $table ) {
			$table->foreign( 'user_id' )
			      ->references( 'id' )
			      ->on( 'users' );
			$table->foreign( 'choice_id' )
			      ->references( 'id' )
			      ->on( 'choices' );
		} );
		Schema::table( 'choices', function ( Blueprint $table ) {
			$table->foreign( 'class_group_id' )
			      ->references( 'id' )
			      ->on( 'class_groups' );
			$table->foreign( 'elective_id' )
			      ->references( 'id' )
			      ->on( 'electives' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table( 'users', function ( Blueprint $table ) {
			$table->dropForeign( [ 'class_group_id' ] );
		} );
		Schema::table( 'results', function ( Blueprint $table ) {
			$table->dropForeign( [ 'user_id' ] );
			$table->dropForeign( [ 'choice_id' ] );
		} );
		Schema::table( 'choices', function ( Blueprint $table ) {
			$table->dropForeign( [ 'elective_id' ] );
			$table->dropForeign( [ 'class_group_id' ] );
		} );
	}
}
