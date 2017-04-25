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
	 * @var Collection
	 */
	private $freeStudents;

	/**
	 * @var  Collection
	 */
	private $studentEnterRank;

	/**
	 * @var Collection
	 */
	private $choicesByUsers;

	/**
	 * @var array
	 */
	private $dividedUsersInChoices;

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
			$this->debugBar->info( '****************************Propose Starter****************************' );
			$this->debugBar->info( 'Student ID: ' . $freeStudent );
			$this->propose_starter( $freeStudent );
		}
		$style = "<style>
		.phpdebugbar-widgets-value.phpdebugbar-widgets-success
		{
			color: #00C853;
		}
		</style>";

		dump( $this->dividedUsersInChoices );
		debug( "" );

		return $style . "ok";
		//return $this->freeStudents;
	}

	/**
	 * Start to proposals
	 *
	 * @param $studentId
	 */
	private function propose_starter( $studentId ) {
		$results = $this->choicesByUsers[ $studentId ];
		/** @var Collection $resultsWithoutLast */
		$resultsWithoutLast = $results;
		$resultsWithoutLast->pop();
		debug( '##########Results of ' . $studentId . '##########' );
		debug( $resultsWithoutLast );

		$hasAcceptedProposal = $this->proposer_handler( $resultsWithoutLast, $studentId );

		if ( ! $hasAcceptedProposal ) {
			$this->debugType( 'error', "Student " . $studentId . " doesn't have a accepted proposal." );
			$this->put_student_first_in_free_students( $studentId );
			$reverseResults = $resultsWithoutLast->reverse();

			$hasAcceptedProposal = $this->proposer_handler( $reverseResults, $studentId );
		}
	}

	private function proposer_handler( $results, $studentId ) {
		foreach ( $results as $result ) {
			debug( '-----Proposing to ' . $result->choice_id . '-----' );
			$likeness = $result->likeness;
			if ( $this->propose_to_choice( $studentId, $result->choice_id, $likeness ) ) {
				$this->debugType( 'success', "Proposal accepted, Stop proposing" );

				return true;
				break;
			}
		}

		return false;
	}

	private function put_student_first_in_free_students( $studentId ) {
		debug( 'Put student ' . $studentId . " first in the enter rank" );

		$this->studentEnterRank->forget( $this->studentEnterRank->search( $studentId ) );
		$this->studentEnterRank->prepend( $studentId );
	}

	/**
	 * Let the student propose to the given choice
	 *
	 * @param $studentId
	 * @param $choiceId
	 * @param $likeness
	 *
	 * @return bool
	 */
	private function propose_to_choice( $studentId, $choiceId, $likeness ) {

		if ( $this->is_choice_accepting( $choiceId ) ) {
			$this->accept_proposal( $studentId, $choiceId, $likeness );
		} else if ( $this->is_lowest_ranking_in_choice( $studentId, $choiceId ) ) {
			$this->debugBar->warning( 'Proposal rejected' );

			return false;
		} else {
			debug( 'Kicking user out off choice' );
			$this->accept_proposal_by_rejecting_lowest( $studentId, $choiceId );

			return false;
		}

		$this->debugType( 'success', "Proposal accepted" );
		$this->remove_user_from_free_students( $studentId );

		return true;
	}

	private function accept_proposal_by_rejecting_lowest( $studentId, $choiceId ) {
		//$this->find_student_with_easy_kick_out( $choiceId );
	}

	private function find_student_with_easy_kick_out( $choiceId ) {
		debug( "Start to find a user with choice that accepts next likeness" );
		dump( "Start to find a user with choice that accepts next likeness" );
		$users = $this->dividedUsersInChoices[ $choiceId ]["users"];
		ksort( $users );
		$usersInChoice = collect( $users );
		$usersInChoice = $usersInChoice->sortBy( 'likeness' );

		$choicesByLikeness = $usersInChoice->groupBy( 'likeness' );
		dump( $choicesByLikeness );
		foreach ( $choicesByLikeness as $likeness => $users ) {
			$users = $users->sortByDesc( 'rank' );
			//dd( $users );

			foreach ( $users as $user ) {
				dump( "User: " . $user["id"] );
				$userPicks = $this->choicesByUsers[ $user["id"] ]->sortBy( 'likeness' );
				dump( $user );
				dump( $userPicks );
				dump( $likeness );

				while ( $userPicks->first()->likeness <= $likeness ) {
					$userPicks->shift();
				}

				dump( $userPicks );
				$firstPick = $userPicks->first();
				if ( ! $this->is_choice_accepting( $firstPick->choice_id ) ) {
					$this->debugBar->warning( "The next choice is not accepting" );
					break;
				}
				$this->debugType( "success", 'Next choice is accepting' );
				$this->remove_user_from_choice( $firstPick->user_id, $choiceId );
				$this->accept_proposal( $firstPick->user_id, $firstPick->choice_id, $firstPick->likeness );
			}

			$choicesByLikeness[ $likeness ] = $users;
		}
		dump( $choicesByLikeness );
	}

	/**
	 * Remove a student from the free students collection of the class
	 *
	 * @param $studentId
	 *
	 * @return bool
	 */
	private function remove_user_from_free_students( $studentId ) {
		debug( "++++Remove Student++++" );
		$studentKey = $this->freeStudents->search( $studentId );
		if ( $studentKey === false ) {
			$this->debugType( 'error', "The student is no longer in the array" );

			return false;
		}

		$this->freeStudents->forget( $studentKey );

		debug( 'Remaining free students: ' . count( $this->freeStudents ) );

		return true;
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
	 * @param $likeness
	 */
	private function accept_proposal( $studentId, $choiceId, $likeness ) {
		debug( "Accepting proposal" );
		$currentChoice                                       = $this->dividedUsersInChoices[ $choiceId ];
		$this->dividedUsersInChoices[ $choiceId ]["users"][] = [
			"rank"     => $this->get_rank_of_user( $studentId ),
			"id"       => $studentId,
			"likeness" => $likeness,
		];
		// Set lowest ranking
		$currentRankOfUser = $this->get_rank_of_user( $studentId );
		debug( "Rank of current user: " . $currentRankOfUser );

		if ( $currentChoice["lowest_rank"] == 0 || $this->is_lowest_ranking_in_choice( $studentId, $choiceId ) ) {
			$this->set_lowest_rank_of_choice( $studentId, $choiceId, $currentRankOfUser );
		}

		$this->set_accepting_state_of_choice( $choiceId, ! $this->is_choice_full( $choiceId ) );
	}

	private function remove_user_from_choice( $userId, $choiceId ) {
		$users       = $this->dividedUsersInChoices[ $choiceId ]["users"];
		$keyToRemove = 0;
		foreach ( $users as $key => $user ) {
			if ( $user["id"] == $userId ) {
				$keyToRemove = $key;
				break;
			}
		}
		dump( $this->dividedUsersInChoices[ $choiceId ]["users"] );
		array_splice( $this->dividedUsersInChoices[ $choiceId ]["users"], $keyToRemove, 1 );
		//dd( $this->dividedUsersInChoices[ $choiceId ]["users"] );

		$this->set_accepting_state_of_choice( $choiceId, ! $this->is_choice_full( $choiceId ) );
	}

	/**
	 * Set a new lowest rank (id, key, rank) to the choice array
	 *
	 * @param $studentId
	 * @param $choiceId
	 * @param $currentRankOfUser
	 */
	private function set_lowest_rank_of_choice( $studentId, $choiceId, $currentRankOfUser ) {
		$studentKey = multidimensionalArraySearch( $studentId, $this->dividedUsersInChoices[ $choiceId ]["users"], 'id' );
		debug( "New lowest user for choice: " . $studentId );
		debug( "New lowest user key for choice: " . $studentKey );
		debug( "New lowest rank for choice: " . $currentRankOfUser );

		$this->dividedUsersInChoices[ $choiceId ]["lowest_rank"]          = $currentRankOfUser;
		$this->dividedUsersInChoices[ $choiceId ]["lowest_rank_user_id"]  = $studentId;
		$this->dividedUsersInChoices[ $choiceId ]["lowest_rank_user_key"] = $studentKey;
	}

	/**
	 * Get the rank of the given user
	 *
	 * @param $studentId
	 *
	 * @return mixed
	 */
	private function get_rank_of_user( $studentId ) {
		return $this->studentEnterRank->search( $studentId );
	}

	/**
	 * Check if the given choice is full (reached maximum), if it is full, set the accepting to false
	 *
	 * @param $choiceId
	 *
	 * @return bool
	 */
	private function is_choice_full( $choiceId ) {
		debug( '+++Check if choice ' . $choiceId . ' is full+++' );
		debug( 'Max nr. of users: ' . $this->dividedUsersInChoices[ $choiceId ]["max"] );
		debug( 'Nr. of users in choice: ' . count( $this->dividedUsersInChoices[ $choiceId ]["users"] ) );

		return count( $this->dividedUsersInChoices[ $choiceId ]["users"] ) == $this->dividedUsersInChoices[ $choiceId ]["max"];
	}

	/**
	 * Set the choice is_accepting to the given state
	 *
	 * @param $choiceId
	 * @param $state
	 */
	private function set_accepting_state_of_choice( $choiceId, $state ) {
		$this->dividedUsersInChoices[ $choiceId ]["is_accepting"] = $state;
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
		debug( '++++Check if user is lower rank++++' );
		$currentRankOfUser  = $this->get_rank_of_user( $studentId );
		$lowestRankOfChoice = $this->dividedUsersInChoices[ $choiceId ]["lowest_rank"];
		debug( 'Rank of user: ' . $currentRankOfUser );
		debug( 'Lowest rank of choice: ' . $lowestRankOfChoice );

		return $currentRankOfUser > $lowestRankOfChoice;
	}


	/**
	 * Add message to debugbar with specific type
	 *
	 * @param $type
	 * @param $data
	 */
	private function debugType( $type, $data ) {
		$this->debugBar->addMessage( $data, $type );
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