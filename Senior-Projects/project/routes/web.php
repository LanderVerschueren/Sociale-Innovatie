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

Auth::routes();

Route::get( '/', 'HomeController@index' );

Route::get( '/index', 'HomeController@index' );

Route::get( '/debug/pick', function () {
	$elective       = \App\Elective::first();
	$divideProvider = new DivideStudent( $elective );

	$divideProvider->debug_random_pick();
} );

Route::get( '/debug/re-pick', function () {
	$elective       = \App\Elective::first();
	$divideProvider = new DivideStudent( $elective );

	$divideProvider->debug_random_re_pick();
} );

Route::get( '/debug/divide', function () {
	$elective       = \App\Elective::first();
	$divideProvider = new DivideStudent( $elective );

	$divideProvider->divide_elective();
});