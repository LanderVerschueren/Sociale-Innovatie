<?php
/**
 * Created by PhpStorm.
 * User: Siebe
 * Date: 25/11/2016
 * Time: 16:06
 */

namespace App\Services;

use App\Elective;
use App\Result;
use App\User;
use Illuminate\Support\Facades\DB;

class DivideStudent {

	private $elective;
	private $choices;
	private $freeStudents;
	private $studentEnterRank;
	private $choicesByUsers;
	private $dividedUsersInChoices;
	private $likenessCounter;

	function __construct( Elective $elective ) {
		$this->elective = $elective;
		$this->choices  = $this->elective->choices;

		foreach ( $this->choices as $choice ) {
			$this->dividedUsersInChoices[ $choice->id ] = [
				"min"          => $choice->minimum,
				"max"          => $choice->maximum,
				"is_accepting" => true,
				"users"        => []
			];
		}
	}

	public function debug_random_re_pick() {
		DB::table( 'results' )->truncate();
		$this->debug_random_pick();
	}

	public function debug_random_pick() {
		$students = User::where( 'is_admin', '0' )->get();

		$choices = $this->elective->choices;

		foreach ( $students as $student ) {
			$choicesRand = $choices->shuffle();
			$picks       = $choicesRand->random( 6 );
			$likeness    = 1;
			foreach ( $picks as $pick ) {
				$student->results()->create( [
					"choice_id" => $pick->id,
					"likeness"  => $likeness,
				] );
				$likeness ++;
			}
		}

		dump( 'ok' );
	}

	public function divide_elective() {
		// Get all results of elective
		$picks                  = $this->elective->results->sortBy( 'id' );
		$choicesByLikeness      = $picks->groupBy( 'likeness' );
		$this->choicesByUsers   = $picks->groupBy( 'user_id' );
		$this->freeStudents     = $this->choicesByUsers->keys();
		$this->studentEnterRank = $this->choicesByUsers->keys();

		foreach ( $choicesByLikeness as $likeness => $data ) {
			$this->likenessCounter[ $likeness ] = 0;
			$this->divide_likeness( $likeness );
		}

		//dump( $this->choices );
		dump( $this->dividedUsersInChoices );
		//dump( $picks );
		dump( $this->likenessCounter );
		dump( $this->choicesByUsers );
		dump( $this->freeStudents );
	}

	private function divide_likeness( $likeness ) {
		foreach ( $this->freeStudents as $freeStudent ) {
			$userId        = $freeStudent;
			$choicesOfUser = $this->choicesByUsers[ $userId ];
			$resultOfUser  = $choicesOfUser->where( 'likeness', $likeness )->first();
			$choiceOfUser  = $resultOfUser->choices;

			$this->put_user_in_choice( $userId, $choiceOfUser->id, $likeness );
			//dump($choiceOfUser);
		}
	}

	private function put_user_in_choice( $userId, $choiceId, $likeness ) {
		$currentChoice = $this->dividedUsersInChoices[ $choiceId ];
		if ( $currentChoice["is_accepting"] || $this->check_if_current_is_preferred( $userId, $choiceId ) !== false ) {
			$this->dividedUsersInChoices[ $choiceId ]["users"][] = [ $userId, $likeness ];
			$this->likenessCounter[ $likeness ]                  = $this->likenessCounter[ $likeness ] + 1;

			$this->check_if_choice_is_full_now( $choiceId );
			$this->remove_user_from_free_students( $userId );

			return true;
		}

		return false;
	}

	private function remove_user_from_free_students( $userId ) {
		unset( $this->freeStudents[ $this->freeStudents->search( $userId ) ] );
	}

	private function check_if_current_is_preferred( $userId, $choiceId ) {
		$choice        = $this->dividedUsersInChoices[ $choiceId ];
		$usersInChoice = $choice["users"];

		$rankOfCurrentUser = $this->studentEnterRank->search( $userId );

		$lowestRank = 0;
		$lowestUser = 0;

		foreach ( $usersInChoice as $user ) {
			$rankOfUser = $this->studentEnterRank->search( $user );
			if ( $rankOfUser > $lowestRank ) {
				$lowestRank = $rankOfUser;
				$lowestUser = $user;
			}
		}

		if ( $rankOfCurrentUser < $lowestRank ) {
			$keyToUnset = array_search( $lowestUser, $usersInChoice );
			unset( $this->dividedUsersInChoices[ $choiceId ]["users"][ $keyToUnset ] );

			return true;
		}

		return false;
	}

	private function check_if_choice_is_full_now( $choiceId ) {
		debug( 'checked' );
		$choice = $this->dividedUsersInChoices[ $choiceId ];

		$max           = $choice["max"];
		$numberOfUsers = count( $choice["users"] );

		if ( $numberOfUsers == $max ) {
			$this->dividedUsersInChoices[ $choiceId ]["is_accepting"] = false;

			return false;
		}

		return true;
	}

}