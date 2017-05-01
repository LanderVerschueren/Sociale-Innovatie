<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPivotTableToChoicesAndClasses extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table( 'choices', function ( Blueprint $table ) {
			$table->dropForeign( [ 'class_group_id' ] );
			$table->dropColumn( 'class_group_id' );
		} );

		Schema::create( 'choice_class_group', function ( Blueprint $table ) {
			$table->increments( 'id' );
			$table->integer( 'choice_id' )->unsigned();
			$table->integer( 'class_group_id' )->unsigned();

			$table->foreign( 'choice_id' )
			      ->references( 'id' )
			      ->on( 'choices' )
                  ->onDelete('cascade');
			
			$table->foreign( 'class_group_id' )
			      ->references( 'id' )
			      ->on( 'class_groups' )
                  ->onDelete('cascade');

			$table->timestamps();
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists( 'choice_class_group' );
		Schema::table( 'choices', function ( Blueprint $table ) {
			$table->integer( 'class_group_id' )->default(1)->unsigned()->after( "settings" );
			$table->foreign( 'class_group_id' )
			      ->references( 'id' )
			      ->on( 'class_groups' )
                  ->onDelete('cascade');
		} );
	}
}
