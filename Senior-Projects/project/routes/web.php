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
Route::get( '/category', 'StudentController@index' );
Route::get( '/{elective}/choices', 'StudentController@choices' );
Route::post( '/rightOrder', 'StudentController@store_choice' );
Route::post( '/storeOrder', 'StudentController@store_order' );

Route::get( '/admin', 'AdminController@login' );
Route::get( '/dashboard', 'AdminController@dashboard' );
Route::get( '/import', 'AdminController@getImportStudents' )->name( 'importStudent' );
Route::post( '/import', 'AdminController@postImportStudents' );
Route::get( '/keuzevak/{name}', 'AdminController@showChoicesFromElective' );
Route::get( '/keuze/{id}', 'AdminController@showResultsFromChoice' );
Route::get( '/klasgroep/{classgroup}', 'AdminController@showStudentsFromClassGroup' );
Route::post( '/addElective', 'AdminController@addElective' );
Route::post( '/addChoice/{name}', 'AdminController@addChoiceToElective' );

Route::get( '/debug/pick/{random?}', function ( $random = false ) {
	$elective       = \App\Elective::first();
	$divideProvider = new DivideStudent( $elective );

	$divideProvider->debug_random_pick( $random );

	return redirect()->route( 'debug-result', [ "elective" => 1 ] );
} );

Route::get( '/debug/results/{elective}/{json?}', function ( \App\Elective $elective, $json = false ) {
	$results           = $elective->results->load( 'choices' )->sortBy( 'id' );
	$choicesByLikeness = $results->groupBy( 'likeness' );
	$picksCounter      = count( $choicesByLikeness );
	$choicesByUsers    = $results->groupBy( 'user_id' );

	if ( $json == "json" ) {
		$response = $choicesByUsers->map( function ( $picks, $key ) {
			$newUser = [
				"user_id" => $key
			];
			foreach ( $picks as $pick ) {
				$newUser["picks"] [  ] = [
					"rank"         => $pick->likeness,
					"name"         => $pick->choices->choice,
					"id_of_choice" => $pick->choices->id,
					"minimum"      => $pick->choices->minimum,
					"maximum"      => $pick->choices->maximum,
				];
			}

			return $newUser;
		} );

		return response()->json( $response->values() );
	}

	return view( 'debug.results', [ "results" => $choicesByUsers, "pickCounter" => $picksCounter ] );
} )->name( 'debug-result' );

Route::get( '/debug/choices/{elective}/{json?}', function ( \App\Elective $elective, $json = false ) {
	$choices = $elective->choices;
	//dump($choices);
	if ( $json == "json" ) {
		$json = [];
		foreach ( $choices as $choice ) {
			$json[  ] = [
				"id"      => $choice->id,
				"name"    => $choice->choice,
				"minimum" => $choice->minimum,
				"maximum" => $choice->maximum
			];
		}

		return response()->json( $json );
	}

	return view( 'debug.choices', [ 'choices' => $choices ] );
} );
Route::get( '/debug/all/{elective}/{json?}', function ( \App\Elective $elective, $json = false ) {
	$results           = $elective->results->load( 'choices' )->sortBy( 'id' );
	$choices           = $elective->choices;
	$choicesByLikeness = $results->groupBy( 'likeness' );
	$picksCounter      = count( $choicesByLikeness );
	$choicesByUsers    = $results->groupBy( 'user_id' );

	if ( $json == "json" ) {
		$response = $choicesByUsers->map( function ( $picks, $key ) {
			$newUser = [
				"user_id" => $key
			];
			foreach ( $picks as $pick ) {
				$newUser["picks"] [  ] = [
					"rank"         => $pick->likeness,
					"name"         => $pick->choices->choice,
					"id_of_choice" => $pick->choices->id,
					"minimum"      => $pick->choices->minimum,
					"maximum"      => $pick->choices->maximum,
				];
			}

			return $newUser;
		} );

		$json = [];
		foreach ( $choices as $choice ) {
			$json[  ] = [
				"id"      => $choice->id,
				"name"    => $choice->choice,
				"minimum" => $choice->minimum,
				"maximum" => $choice->maximum
			];
		}

		return response()->json( [ "choices" => $json, "picks" => $response->values() ] );
	}

	return view( 'debug.all', [ "results" => $choicesByUsers, "pickCounter" => $picksCounter, 'choices' => $choices ] );
} );

Route::get( '/debug/divide', function () {
	$elective       = \App\Elective::first();
	$divideProvider = new DivideStudent( $elective );

	return $divideProvider->divide_elective();
} );
