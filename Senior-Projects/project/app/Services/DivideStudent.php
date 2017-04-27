<?php
/**
 * Created by PhpStorm.
 * User: Siebe
 * Date: 25/11/2016
 * Time: 16:06
 */

namespace App\Services;

use App\ClassAmount;
use App\Elective;
use App\Result;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DivideStudent {

	/**
	 * @var Elective
	 */
	private $elective;

	/**
	 * @var \App\Choice[]|\Illuminate\Database\Eloquent\Collection
	 */
	private $choices;

	/**
	 * @var array
	 */
	private $dividedUsersInChoices;

	/**
	 * @var Collection
	 */
	private $picks;

	/**
	 * @var Collection
	 */
	private $usersData;

	/**
	 * @var \Barryvdh\Debugbar\LaravelDebugbar|\Illuminate\Foundation\Application|mixed
	 */
	private $debugBar;

	function __construct( Elective $elective ) {
		$this->debugBar = app( 'debugbar' );

		$this->elective = $elective;
		$this->choices  = $this->elective->choices;

		foreach ( $this->choices as $choice ) {
			$this->dividedUsersInChoices[ $choice->id ] = [
				"min"                  => $choice->minimum,
				"max"                  => $choice->maximum,
				"lowest_rank"          => - 1,
				"lowest_rank_user_id"  => 0,
				"lowest_rank_user_key" => - 1,
				"is_accepting"         => true,
				"users"                => []
			];
		}
	}

	/**
	 * Function to (re-)pick the results for all the users
	 *
	 * @param $random
	 */
	public function debug_random_pick( $random ) {
		DB::table( 'results' )->truncate();
		$students = User::where( 'is_admin', '0' )->get()->take( rand( 100, 150 ) )->shuffle();

		$choices = $this->elective->choices;

		foreach ( $students as $student ) {
			$choicesRand = $choices;
			if ( $random ) {
				//dump( "Going full random" );
				$choicesRand = $choices->shuffle();
			}
			$picks    = $choicesRand->random( 6 );
			$likeness = 1;
			foreach ( $picks as $pick ) {
				$student->results()->create( [
					"choice_id" => $pick->id,
					"likeness"  => $likeness,
				] );
				$likeness ++;
			}
		}

		dump( "Full random: " . ( $random ? "Yes" : "No" ) );
		dump( 'Picked' );

	}

	/**
	 * Start dividing all users
	 *
	 * @return string
	 */
	public function divide_elective() {
		// Get all user data
		$userData       = $this->getUserData();
		$userData       = $userData->groupBy( 'number_of_choices' )->sort();
		$sortedUserData = collect( [] );
		foreach ( $userData as $key => $collection ) {
			/** @var Collection $collection */
			$sortedUserData = $sortedUserData->merge( $collection->sortBy( 'id_of_pick' ) );
		}

		$this->usersData = $sortedUserData;

		foreach ( $this->usersData as $key => $user ) {
			$this->divideUser( $user, $key );
		}

		$style = "<style>
		.phpdebugbar-widgets-value.phpdebugbar-widgets-success
		{
			color: #00C853;
		}
		</style>";

		return $style . "ok";
	}

	/**
	 * @return Collection
	 */
	private function getUserData() {
		$this->picks    = $this->elective->results->load( 'choices' )->sortBy( 'id' );
		$choicesByUsers = $this->picks->groupBy( 'user_id' );

		$electiveId = $this->elective->id;

		$userData = $choicesByUsers->map( function ( $picks, $key ) use ( $electiveId ) {
			$user = User::find( $key )->load( 'class_group' );

			$numberOfChoices = ClassAmount::where( 'elective_id', '=', $electiveId )
			                              ->where( 'class_id', '=', $user->class_group->class_id )
			                              ->get( [ 'amount' ] )->first()->amount;

			$newUser = [
				"user_id"         => $key,
				"school_id"       => $user->student_id,
				"number_of_choices" => $numberOfChoices,
				"id_of_pick"        => NULL,
				"divide_status"    => [
					"user_is_happy"    => false,
					"user_is_divided"  => false,
					"divide_likeness" => NULL
				]
			];

			foreach ( $picks as $pick ) {
				/** @var Result $pick */
				$newUser['idOfPick'] = $newUser['idOfPick'] ?? $pick->id;
				$newUser["picks"] [] = [
					"can_be_picked"  => true,
					"rank"         => $pick->likeness,
					"name"         => $pick->choices->choice,
					"id_of_choice" => $pick->choices->id,
				];
			}

			return $newUser;
		} );

		return $userData;
	}

	/**
	 * @param array $user
	 * @param int $userRank
	 */
	private function divideUser( array $user, $userRank ) {
		dump($this->choices);
		dump($this->dividedUsersInChoices);
		dd( $user );
	}

	private function proposeToChoice($choiceId, $userId, $userRank, $likeness){
		if(!isset($this->dividedUsersInChoices[$choiceId])){
			abort(404, "The choice is not available");
		}

		$currentChoice = $this->dividedUsersInChoices[$choiceId];

		if($currentChoice['is_accepting']){
			$this->addUserToChoice($choiceId, $userId, $userRank, $likeness);
		}
	}

	private function addUserToChoice($choiceId, $userId, $userRank, $likeness){
		$this->dividedUsersInChoices[$choiceId]["users"][] = [
			"rank" => $userRank,
			"id" => $userId,
			"likeness" => $likeness
		] ;
	}
}