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
				"min"                 => $choice->minimum,
				"max"                 => $choice->maximum,
				"lowest_rank"         => 0,
				"lowest_rank_user_id" => 0,
				"is_accepting"        => true,
				"users"               => []
			];
		}
	}

	/**
	 * Function to (re-)pick the results for aal the users
	 *
	 * @param $random
	 */
	public function debug_random_pick( $random ) {
		DB::table( 'results' )->truncate();
		$students = User::where( 'is_admin', '0' )->get()->shuffle();

		$choices = $this->elective->choices;

		foreach ( $students as $student ) {
			$choicesRand = $choices;
			if ( $random ) {
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

		dump( 'ok' );
	}

	/**
	 * Start dividing all users
	 *
	 * @return string
	 */
	public function divide_elective() {
		$picks             = $this->elective->results->sortBy( 'id' );
		$choicesByLikeness = $picks->groupBy( 'likeness' );

		$this->choicesByUsers   = $picks->groupBy( 'user_id' );
		$this->freeStudents     = $this->choicesByUsers->keys();
		$this->studentEnterRank = $this->choicesByUsers->keys();

		debug( '=========================Divided Users in Choices=========================' );
		debug( $this->dividedUsersInChoices );
		debug( '=========================Choices By Likeness=========================' );
		debug( $choicesByLikeness );
		debug( '=========================Choices By Users=========================' );
		debug( $this->choicesByUsers );
		debug( '=========================Free Students (Enter Rank)=========================' );
		debug( $this->studentEnterRank );

		debug( '=========================Start proposing=========================' );
		foreach ( $this->freeStudents as $freeStudent ) {
			debug( '***************Propose Starter***************' );
			debug( 'Student ID: ' . $freeStudent );
			$this->propose_starter( $freeStudent );
		}

		return "ok";
		//return $this->freeStudents;
	}

	/**
	 * Start to proposals
	 *
	 * @param $studentId
	 */
	private function propose_starter( $studentId ) {
		$results = $this->choicesByUsers[ $studentId ];
		debug( '##########Results of ' . $studentId . '##########' );
		debug( $results );

		foreach ( $results as $result ) {
			debug( '-----Proposing to ' . $result->choice_id . '-----' );
			$this->propose_to_choice( $studentId, $result->choice_id );
		}
	}

	/**
	 * Let the student propose to the given choice
	 *
	 * @param $studentId
	 * @param $choiceId
	 */
	private function propose_to_choice( $studentId, $choiceId ) {
		$currentChoice = $this->dividedUsersInChoices[ $choiceId ];

		if ( $this->is_choice_accepting( $choiceId ) ) {
			$this->accept_proposal( $studentId, $choiceId );
		} else {
			if($this->is_lowest_ranking_in_choice( $studentId, $choiceId ))
			{
				// Propose fail
			}
			else{
				// Accept proposal and reject lowest
			}
		}
	}

	/**
	 * Check if the choice is accepting, if it is not full
	 *
	 * @param $choiceId
	 *
	 * @return mixed
	 */
	private function is_choice_accepting( $choiceId ) {
		$currentChoice = $this->dividedUsersInChoices[ $choiceId ];

		debug( 'Choice is accepting: ' . boolh( $currentChoice["is_accepting"] ) );

		return $currentChoice["is_accepting"];
	}

	/**
	 * Accept the proposal of the user, put it in the "users" array and set the new lowest rank if necessary
	 *
	 * @param $studentId
	 * @param $choiceId
	 */
	private function accept_proposal( $studentId, $choiceId ) {
		$currentChoice            = $this->dividedUsersInChoices[ $choiceId ];
		$currentChoice["users"][] = $studentId;
		// Set lowest ranking
		$currentRankOfUser = $this->get_rank_of_user( $studentId );

		if ( $currentChoice["lowest_rank"] == 0 || $currentChoice["lowest_rank"] > $currentRankOfUser ) {
			$this->dividedUsersInChoices[ $choiceId ]["lowest_rank"]         = $currentRankOfUser;
			$this->dividedUsersInChoices[ $choiceId ]["lowest_rank_user_id"] = $studentId;
		}
	}

	/**
	 * Get the rank of the given user
	 *
	 * @param $student_id
	 *
	 * @return mixed
	 */
	private function get_rank_of_user( $student_id ) {
		return $this->studentEnterRank->search( $student_id );
	}

	/**
	 * Check if the given choice is full (reached maximum), if it is full, set the accepting to false
	 *
	 * @param $choice_id
	 */
	private function is_choice_full($choice_id) {

	}

	/**
	 * Check if the user is the lowest ranking in the choice
	 *
	 * @param $studentId
	 * @param $choiceId
	 *
	 * @return bool
	 */
	private function is_lowest_ranking_in_choice( $studentId, $choiceId ) {
		return true;
	}
	/*

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

	*/

}