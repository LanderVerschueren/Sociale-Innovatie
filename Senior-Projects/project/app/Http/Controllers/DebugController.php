<?php

namespace App\Http\Controllers;

use App\Elective;
use App\Services\DivideStudent;
use App\User;
use Illuminate\Http\Request;

class DebugController extends Controller {
	public function divide() {
		$elective       = Elective::first();
		$divideProvider = new DivideStudent( $elective );

		return $divideProvider->divide_elective();
	}

	public function random( $random = false ) {
		$elective       = Elective::first();
		$divideProvider = new DivideStudent( $elective );

		$divideProvider->debug_random_pick( $random );

		return redirect()->route( 'debug-result', [ "elective" => 1 ] );
	}

	public function users( Elective $elective ) {
		$results        = $elective->results->load( 'choices' )->sortBy( 'id' );
		$choicesByUsers = $results->groupBy( 'user_id' );

		$response           = $choicesByUsers->map( function ( $picks, $key ) {
			$user    = User::find( $key );
			$newUser = [
				"user_id"   => $key,
				"school_id" => $user->student_id,
				"numberOfChoices" => 1
			];

			return $newUser;
		} );
		$response_indexFree = [];
		foreach ( $response as $user ) {
			//dd($user["picks"]);
			$response_indexFree[] = $user;
		}

		return response()->json( $response_indexFree );
	}

	public function all( Elective $elective, $json = false ) {
		$results           = $elective->results->load( 'choices' )->sortBy( 'id' );
		$choices           = $elective->choices;
		$choicesByLikeness = $results->groupBy( 'likeness' );
		$picksCounter      = count( $choicesByLikeness );
		$choicesByUsers    = $results->groupBy( 'user_id' );

		if ( $json == "json" || $json == "json-free" ) {
			$response = $choicesByUsers->map( function ( $picks, $key ) {
				$newUser = [
					"user_id" => $key
				];
				foreach ( $picks as $pick ) {
					$newUser["picks"] [] = [
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
				$json[] = [
					"id"      => $choice->id,
					"name"    => $choice->choice,
					"minimum" => $choice->minimum,
					"maximum" => $choice->maximum
				];
			}

			if ( $json == "json-free" ) {
				$response_indexFree = [];
				foreach ( $response as $user ) {
					//dd($user["picks"]);
					$response_indexFree[] = $user["picks"];
				}

				return response()->json( $response_indexFree );
			}

			return response()->json( [ "choices" => $json, "picks" => $response->values() ] );
		}

		return view( 'debug.all', [
			"results"     => $choicesByUsers,
			"pickCounter" => $picksCounter,
			'choices'     => $choices
		] );
	}

	public function choices( Elective $elective, $json = false ) {
		$choices = $elective->choices;
		//dump($choices);
		if ( $json == "json" || $json == "json-free" ) {
			$json = [];
			foreach ( $choices as $choice ) {
				$json[] = [
					"id"      => $choice->id,
					"name"    => $choice->choice,
					"minimum" => $choice->minimum,
					"maximum" => $choice->maximum
				];
			}

			return response()->json( $json );
		}

		return view( 'debug.choices', [ 'choices' => $choices ] );
	}

	public function results( Elective $elective, $json = false ) {
		$results           = $elective->results->load( 'choices' )->sortBy( 'id' );
		$choicesByLikeness = $results->groupBy( 'likeness' );
		$picksCounter      = count( $choicesByLikeness );
		$choicesByUsers    = $results->groupBy( 'user_id' );

		if ( $json == "json" || $json == "json-free" ) {
			$response = $choicesByUsers->map( function ( $picks, $key ) {
				$newUser = [
					"user_id" => $key
				];
				foreach ( $picks as $pick ) {
					$newUser["picks"] [] = [
						"rank"         => $pick->likeness,
						"name"         => $pick->choices->choice,
						"id_of_choice" => $pick->choices->id,
						"minimum"      => $pick->choices->minimum,
						"maximum"      => $pick->choices->maximum,
					];
				}

				return $newUser;
			} );

			if ( $json == "json-free" ) {
				$response_indexFree = [];
				foreach ( $response as $user ) {
					//dd($user["picks"]);
					$response_indexFree[] = $user["picks"];
				}

				return response()->json( $response_indexFree );
			}

			return response()->json( $response->values() );
		}

		return view( 'debug.results', [ "results" => $choicesByUsers, "pickCounter" => $picksCounter ] );
	}
}
