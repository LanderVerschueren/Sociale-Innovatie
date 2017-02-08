<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElectiveClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'elective_class_amount', function ( Blueprint $table ) {
            $table->increments( 'id' );
            $table->integer( 'elective_id' )->unsigned();
            $table->integer( 'class_id' )->unsigned();

            $table->foreign( 'elective_id' )
                ->references( 'id' )
                ->on( 'electives' );
            $table->foreign( 'class_id' )
                ->references( 'id' )
                ->on( 'classes' );
            $table->integer( 'amount' );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('elective_class_amount');
    }
}
