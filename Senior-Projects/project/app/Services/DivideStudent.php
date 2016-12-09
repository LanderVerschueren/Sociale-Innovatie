<?php
/**
 * Created by PhpStorm.
 * User: Siebe
 * Date: 25/11/2016
 * Time: 16:06
 */

namespace App\Services;

use App\Elective;
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

	public function debug_random_re_pick($random) {
		DB::table( 'results' )->truncate();
		$this->debug_random_pick($random);
	}

	public function debug_random_pick($random) {
		$students = User::where( 'is_admin', '0' )->get()->shuffle();

		$choices = $this->elective->choices;

		foreach ( $students as $student ) {
			$choicesRand = $choices;
			if($random) {
				$choicesRand = $choices->shuffle();
			}
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
		$picks             = $this->elective->results->sortBy( 'id' );
		$choicesByLikeness = $picks->groupBy( 'likeness' );
		// Remove the last choice
		//$choicesByLikeness->pop();
		$this->choicesByUsers   = $picks->groupBy( 'user_id' );
		$this->freeStudents     = $this->choicesByUsers->keys();
		$this->studentEnterRank = $this->choicesByUsers->keys();

		$lastLikeness = $choicesByLikeness->reverse()->keys()->first();
		foreach ( $choicesByLikeness as $likeness => $data ) {
			if ( $likeness !== $lastLikeness ) {
				$this->likenessCounter[ $likeness ] = 0;
				$this->divide_likeness( $likeness );
			}
		}

		dump( $this->dividedUsersInChoices );
		dump( $this->likenessCounter );
		dump( $this->choicesByUsers );
		dump( $this->freeStudents );

		if ( count( $this->freeStudents ) > 0 ) {
			dump( "=================Final=================" );
			foreach ( $this->freeStudents as $freeStudent ) {
				dump( "================New student================" );
				$picksOfUser            = $this->choicesByUsers[ $freeStudent ];
				$picksOfUserWithoutLast = $picksOfUser;
				$picksOfUserWithoutLast->pop();
				$lastPick = $picksOfUserWithoutLast->last();
				$choiceId = $lastPick->choices->id;
				dump( $freeStudent );
				dump( $choiceId );
				$usersInPick = $this->dividedUsersInChoices[ $choiceId ]["users"];
				$likeness    = 1;
				do {
					$keys = $this->get_keys_of_likeness( $usersInPick, $likeness );
					$likeness ++;
					if ( $likeness > $lastLikeness ) {
						dump( "No users" );
						break;
					}
				} while ( ! count( $keys ) );

				$likeness --;
				dump( "Current likeness: " . $likeness );
				$keys = array_reverse( $keys );

				foreach ( $keys as $key ) {
					$userIdToChange = $usersInPick[ $key ]["user_id"];
					// Get picks of user
					$picksOfUserToChange = $this->choicesByUsers[ $userIdToChange ];
					dump( $picksOfUserToChange );
				}
				//dump( $keys );
				//dump( $choiceId );
			}
		}
	}

	private function get_keys_of_likeness( $usersInPick, $likeness ) {
		return array_keys( array_column( $usersInPick, 'likeness' ), $likeness );
	}

	private function divide_likeness( $likeness ) {
		foreach ( $this->freeStudents as $freeStudent ) {
			$userId        = $freeStudent;
			$choicesOfUser = $this->choicesByUsers[ $userId ];
			$resultOfUser  = $choicesOfUser->where( 'likeness', $likeness )->first();
			$choiceOfUser  = $resultOfUser->choices;
			$idOfPick      = $resultOfUser->id;

			$this->put_user_in_choice( $userId, $choiceOfUser->id, $likeness, $idOfPick );
			//dump($choiceOfUser);
		}
	}

	private function put_user_in_choice( $userId, $choiceId, $likeness, $idOfPick ) {
		$currentChoice = $this->dividedUsersInChoices[ $choiceId ];
		if ( $currentChoice["is_accepting"] || $this->check_if_current_is_preferred( $userId, $choiceId ) !== false ) {
			$this->dividedUsersInChoices[ $choiceId ]["users"][] = [
				"user_id"    => $userId,
				"likeness"   => $likeness,
				"id_of_pick" => $idOfPick
			];
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

		//dd("not");

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