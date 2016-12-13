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
Route::get('/category', 'StudentController@category');
Route::get( '/home', 'StudentController@index' );
Route::get('/{elective}/choices', 'StudentController@choices');
Route::post('/category', 'StudentController@store_choice');

Route::get( '/admin', 'AdminController@login');
Route::get( '/dashboard', 'AdminController@dashboard');
Route::get( '/keuzevak/{name}', 'AdminController@showChoicesFromElective');
Route::get( '/keuze/{id}', 'AdminController@showResultsFromChoice');
Route::get( '/klasgroep/{classgroup}', 'AdminController@showStudentsFromClassGroup');


Route::get( '/debug/pick/{random?}', function ($random = false) {
	$elective       = \App\Elective::first();
	$divideProvider = new DivideStudent( $elective );

	$divideProvider->debug_random_pick($random);
} );

Route::get( '/debug/results/{elective}', function (\App\Elective $elective) {
	$results           = $elective->results->sortBy( 'id' );
	$choicesByLikeness = $results->groupBy( 'likeness' );
	$picksCounter      = count( $choicesByLikeness );
	$choicesByUsers    = $results->groupBy( 'user_id' );

	//dd($choicesByUsers);

	return view( 'debug.results', [ "results" => $choicesByUsers, "pickCounter" => $picksCounter ] );
} );

Route::get( '/debug/divide', function () {
	$elective       = \App\Elective::first();
	$divideProvider = new DivideStudent( $elective );

	return $divideProvider->divide_elective();
} );