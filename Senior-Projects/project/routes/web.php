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
Route::group(['middleware' => 'auth'], function () {
    Route::get( '/category', 'StudentController@index' );
    Route::post( '/rightOrder', 'StudentController@store_choice' );
    Route::post( '/storeOrder', 'StudentController@store_order' );
    Route::get( '/{elective}/choices', 'StudentController@choices' );
    Route::get('/{elective}/consultCoices', 'StudentController@consultCoices');
});

Route::get( '/admin', 'AdminController@login' );
Route::group(['middleware' => 'admin'], function () {
    Route::get( '/dashboard', 'AdminController@dashboard' );
    Route::get( '/import', 'AdminController@getImportStudents' )->name( 'importStudent' );
    Route::post( '/import', 'AdminController@postImportStudents' );
    Route::get( '/keuzevak/{name}', 'AdminController@showChoicesFromElective' );
    Route::get( '/keuze/{name}', 'AdminController@showResultsFromChoice' );
    Route::get('/deleteChoice/{id}', 'AdminController@deleteChoice');
    Route::get( '/klasgroep/{classgroup}', 'AdminController@showStudentsFromClassGroup' );
    Route::post( '/addElective', 'AdminController@addElective' );
    Route::post( '/addChoice/{name}', 'AdminController@addChoiceToElective' );
    Route::post( '/updateChoice/{name}', 'AdminController@addChoiceToElective' );
    Route::post('/giveAmountToElective/{id}', 'AdminController@giveAmountToClasses');

    Route::get('/divide/{electiveId}', 'AdminController@divideElective')->name('divideElective');
});

Route::get( '/debug/pick/{random?}', "DebugController@random" );
Route::get( '/debug/results/{elective}/{json?}', "DebugController@results" )->name( 'debug-result' );
Route::get( '/debug/choices/{elective}/{json?}', "DebugController@choices" );
Route::get( '/debug/all/{elective}/{json?}', "DebugController@all" );
Route::get( '/debug/divide', "DebugController@divide" );
Route::get( '/debug/users/{elective}', "DebugController@users" );
