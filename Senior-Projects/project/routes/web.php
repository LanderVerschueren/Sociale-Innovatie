<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

use App\Services\DivideStudent;

Route::get( '/', function () {
	return view( 'welcome' );
} );

Auth::routes();

Route::get( '/home', 'HomeController@index' );

Route::get( '/debug/pick', function () {
	$elective       = \App\Elective::first();
	$divideProvider = new DivideStudent( $elective );

	$divideProvider->debug_random_pick();
} );

Route::get( '/debug/divide', function () {
	$elective       = \App\Elective::first();
	$divideProvider = new DivideStudent( $elective );

	$divideProvider->divide_elective();
});