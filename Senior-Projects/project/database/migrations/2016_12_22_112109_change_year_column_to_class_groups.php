<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeYearColumnToClassGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('classes', function (Blueprint $table) {
		    $table->dropColumn( 'year' );
	    });
        Schema::table('class_groups', function (Blueprint $table) {
	        $table->integer( 'year' )->unsigned()->after('class_group');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('classes', function (Blueprint $table) {
		    $table->integer( 'year' )->unsigned()->after('id');
	    });
        Schema::table('class_groups', function (Blueprint $table) {
	        $table->dropColumn( 'year' );
        });
    }
}
