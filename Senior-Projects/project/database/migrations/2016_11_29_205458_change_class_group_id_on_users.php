<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeClassGroupIdOnUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up() {
		Schema::table( 'users', function ( Blueprint $table ) {
			$table->dropForeign( [ 'class_group_id' ] );
			$table->integer( 'class_group_id' )->unsigned()->nullable()->change();
			$table->foreign( 'class_group_id' )
			      ->references( 'id' )
			      ->on( 'class_groups' );
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
			$table->integer( 'class_group_id' )->unsigned()->change();
			$table->foreign( 'class_group_id' )
			      ->references( 'id' )
			      ->on( 'class_groups' );
		} );
	}
}
