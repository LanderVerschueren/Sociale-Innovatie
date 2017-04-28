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
	 * Fetch an array of the users data and picks
	 *
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
				"user_id"           => $key,
				"school_id"         => $user->student_id,
				"number_of_choices" => $numberOfChoices,
				"id_of_pick"        => NULL,
				"divide_status"     => $this->makeDivideStatusArray()
			];

			foreach ( $picks as $pick ) {
				/** @var Result $pick */
				$newUser['id_of_pick'] = $newUser['id_of_pick'] ?? $pick->id;
				$newUser["picks"] []   = [
					"can_be_picked" => true,
					"rank"          => $pick->likeness,
					"name"          => $pick->choices->choice,
					"id_of_choice"  => $pick->choices->id,
				];
			}

			return $newUser;
		} );

		return $userData;
	}

	/**
	 * Start dividing all users
	 *
	 * @return string
	 */
	public function divide_elective() {
		// Get all user data
		debug( 'Start dividing the elective' );
		debug( 'Fetch the user data' );
		$userData       = $this->getUserData();
		$userData       = $userData->groupBy( 'number_of_choices' )->sort();
		$sortedUserData = collect( [] );
		foreach ( $userData as $key => $collection ) {
			/** @var Collection $collection */
			$sortedUserData = $sortedUserData->merge( $collection->sortBy( 'id_of_pick' ) );
		}

		$this->usersData = $sortedUserData;

		debug( 'Start dividing users' );
		foreach ( $this->usersData as $key => $user ) {
			$keyOfPick = $this->divideUser( $user, $key );
			if ( $keyOfPick !== false ) {
				$temp                                         = $this->usersData[ $key ];
				$temp['picks'][ $keyOfPick ]['can_be_picked'] = false;
				$this->usersData[ $key ]                      = $temp;

				$this->updateDivideStatusOfUser( $key );
			}
		}

		dump( $this->dividedUsersInChoices );
		dump( $this->usersData );

		$style = "<style>
		pre.sf-dump{
		z-index: 5 !important;
		}
		.phpdebugbar-widgets-value.phpdebugbar-widgets-success
		{
			color: #00C853;
		}
		</style>";

		return $style . "ok";
	}

	/**
	 * Magic where the user gets a choice
	 */
	private function divideUser( array $user, $userRank ) {
		debug( sprintf( 'Dividing user(%s): %s', $userRank, $user['user_id'] ) );
		dump( sprintf( 'Dividing user(%s): %s', $userRank, $user['user_id'] ) );
		$userId = $user['user_id'];
		$picks  = $user['picks'];
		dump( $picks );
		foreach ( $picks as $key => $pick ) {
			debug( sprintf( '| | Checking choice %d: %s', $key, $pick['name'] ) );
			if ( ! $pick['can_be_picked'] ) {
				continue;
			}

			if ( $this->proposeToChoice( $pick['id_of_choice'], $userId, $userRank, $pick['rank'] ) ) {
				return $key;
			}
		}

		return false;
	}

	/**
	 * Ask a choice if it is still available
	 */
	private function proposeToChoice( $choiceId, $userId, $userRank, $likeness ) {
		if ( ! isset( $this->dividedUsersInChoices[ $choiceId ] ) ) {
			abort( 404, "The choice is not available" );
		}

		$currentChoice = $this->dividedUsersInChoices[ $choiceId ];

		if ( $currentChoice['is_accepting'] ) {
			$newKey = $this->addUserToChoice( $choiceId, $userId, $userRank, $likeness );
			$this->updateChoiceProperties( $choiceId, $userId, $userRank, $newKey );

			return true;
		}

		return false;
	}

	private function addUserToChoice( $choiceId, $userId, $userRank, $likeness ) {
		$newKey                                                       = count( $this->dividedUsersInChoices[ $choiceId ]["users"] );
		$this->dividedUsersInChoices[ $choiceId ]["users"][ $newKey ] = [
			"rank"     => $userRank,
			"id"       => $userId,
			"likeness" => $likeness
		];

		return $newKey;
	}

	private function updateChoiceProperties( $choiceId, $userId, $userRank, $newKey ) {
		$currentChoice = $this->dividedUsersInChoices[ $choiceId ];

		$max           = $currentChoice['max'];
		$users         = $currentChoice['users'];
		$numberOfUsers = count( $users );

		if ( $this->isNewLowestRanking( $choiceId, $userRank ) ) {
			$this->setLowestRankingOfChoice( $choiceId, $userId, $userRank, $newKey );
		}

		if ( $numberOfUsers == $max ) {
			$this->stopAcceptingUsers( $choiceId );
		}
	}

	private function updateDivideStatusOfUser( $keyInUsersData ) {
		$currentUser           = $this->usersData[ $keyInUsersData ];
		$totalNumberOfPicks    = count( $currentUser['picks'] );
		$requiredNumberOfPicks = $currentUser['number_of_choices'];
		$pickedChoices         = $this->getPickedChoicesOfUser( $currentUser );

		$hasAllPicks = count( $pickedChoices ) == $requiredNumberOfPicks;

		$lowestLikeness = NULL;

		foreach ( $pickedChoices as $choice ) {
			if ( $lowestLikeness === NULL || $choice['rank'] < $lowestLikeness ) {
				$lowestLikeness = $choice['rank'];
			}
		}

		$temp                               = $this->usersData[ $keyInUsersData ];
		$temp['divide_status']              = $this->makeDivideStatusArray( $lowestLikeness <= $totalNumberOfPicks / 2, $hasAllPicks, $lowestLikeness );
		$this->usersData[ $keyInUsersData ] = $temp;
	}

	private function makeDivideStatusArray( $userIsHappy = false, $userIsDivided = false, $divideLikeness = NULL ) {
		return [
			"user_is_happy"   => $userIsHappy,
			"user_is_divided" => $userIsDivided,
			"divide_likeness" => $divideLikeness
		];
	}

	private function stopAcceptingUsers( $choiceId ) {
		$this->dividedUsersInChoices[ $choiceId ]['is_accepting'] = false;
	}

	private function isNewLowestRanking( $choiceId, $userRank ) {
		$lowestRankOfChoice = $this->getLowestRankingOfChoice( $choiceId );

		return $userRank > $lowestRankOfChoice;
	}

	private function setLowestRankingOfChoice( $choiceId, $userId, $userRank, $userKey ) {
		$this->dividedUsersInChoices[ $choiceId ]['lowest_rank_user_id']  = $userId;
		$this->dividedUsersInChoices[ $choiceId ]['lowest_rank_user_key'] = $userKey;
		$this->dividedUsersInChoices[ $choiceId ]['lowest_rank']          = $userRank;
	}

	private function getLowestRankingOfChoice( $choiceId ) {
		return $this->dividedUsersInChoices[ $choiceId ]['lowest_rank'];
	}

	private function getPickedChoicesOfUser( array $userData ) {
		$picked = [];
		foreach ( $userData['picks'] as $pick ) {
			if ( $pick['can_be_picked'] == false ) {
				$picked[] = $pick;
			}
		}

		return $picked;
	}
}