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

	/** @var  int */
	private $retryCounter;

	/**
	 * @var \Barryvdh\Debugbar\LaravelDebugbar|\Illuminate\Foundation\Application|mixed
	 */
	private $debugBar;

	function __construct( Elective $elective ) {
		ini_set('max_execution_time', 0);
		$this->debugBar = app( 'debugbar' );

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

		$this->retryCounter = 0;
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
		$loopData = $sortedUserData;

		debug( 'Start dividing users' );
		while($this->checkIfAllUsersDivided()) {
			if($this->retryCounter !== 0){
				$loopData = $this->generateNewUserData();
			}

			dump(count($loopData));

			foreach ( $loopData as $key => $user ) {
				$keyOfUser = $this->getKeyOfUser($user['user_id']);
				$keyOfPick = $this->divideUser( $user, $keyOfUser );
				if ( $keyOfPick !== false ) {
					$temp                                         = $this->usersData[ $keyOfUser ];
					$temp['picks'][ $keyOfPick ]['can_be_picked'] = false;
					$this->usersData[ $keyOfUser ]                      = $temp;

					$this->updateDivideStatusOfUser( $keyOfUser );
				} else {
					$this->debugBar->error( sprintf( 'User (%s) not divided', $key ) );
				}
			}
			$this->retryCounter++;
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
	 * Magic where the user gets a choice
	 */
	private function divideUser( array $user, $userRank ) {
		debug( sprintf( 'Dividing user(%s): %s', $userRank, $user['user_id'] ) );
		//dump( sprintf( 'Dividing user(%s): %s', $userRank, $user['user_id'] ) );
		$userId = $user['user_id'];
		$picks  = $user['picks'];

		foreach ( $picks as $key => $pick ) {
			debug( sprintf( '| | Checking choice %d: %s', $key, $pick['name'] ) );
			if ( ! $pick['can_be_picked'] ) {
				continue;
			}

			if ( $this->proposeToChoice( $pick['id_of_choice'] ) ) {
				$this->addUserToChoice( $pick['id_of_choice'], $userId, $userRank, $pick['rank'] );
				$this->updateChoiceProperties( $pick['id_of_choice'] );

				return $key;
			}
		}

		// User is not divided
		foreach ( $picks as $key=>$pick ) {
			if($this->handleSecondTryForUser( $pick['id_of_choice'], $pick, $userId, $userRank )){
				return $key;
			};
		}

		return false;
	}

	/**
	 * Ask a choice if it is still available
	 */
	private function proposeToChoice( $choiceId ) {
		if ( ! isset( $this->dividedUsersInChoices[ $choiceId ] ) ) {
			abort( 404, "The choice is not available" );
		}

		$currentChoice = $this->dividedUsersInChoices[ $choiceId ];

		return $currentChoice['is_accepting'];
	}

	private function handleSecondTryForUser( $choiceId, $pick, $userId, $userRank ) {
		$currentChoice = $this->dividedUsersInChoices[ $choiceId ];

		$sortedUsers = $currentChoice['users'];
		usort( $sortedUsers, function ( $a, $b ) {
			return $b['rank'] <=> $a['rank'];
		} );

		foreach ( $sortedUsers as $user ) {
			// Check for free swap for user
			$checkForUserId = $user['rank'];
			$picksToCheck   = $this->getFreePicksOfUser( $checkForUserId );

			list( $idOfChoice, $likenessOfChoice ) = $this->checkIfFreeSwap( $picksToCheck );

			if ( $idOfChoice !== NULL ) {
				$userDetails = $this->removeUserFromChoice( $choiceId, $user['id'] );
				$this->addUserToChoice( $idOfChoice, $userDetails['id'], $userDetails['rank'], $likenessOfChoice );
				$this->updateChoiceProperties( $idOfChoice );
				$this->updateChoiceProperties( $choiceId );
				if ( $this->proposeToChoice( $choiceId ) ) {
					$this->addUserToChoice( $choiceId, $userId, $userRank, $pick['rank'] );
					$this->updateChoiceProperties( $choiceId );

					return true;
				}
			}
		}

		return false;

	}

	private function checkIfFreeSwap( $picks ) {
		foreach ( $picks as $pick ) {
			if ( $this->proposeToChoice( $pick['id_of_choice'] ) ) {
				return [ $pick['id_of_choice'], $pick['rank'] ];
			}
		}

		return [ NULL, NULL ];
	}

	private function checkIfAllUsersDivided(){
		if($this->retryCounter == 100){
			dump('error');
			dump($this->dividedUsersInChoices);
			dump($this->generateNewUserData($this->usersData));
			dd($this->usersData);
		}
		foreach ($this->usersData as $user){
			if($user['divide_status']['user_is_divided'] === false){
				return true;
			}
		}

		return $this->retryCounter === 0;
	}

	private function addUserToChoice( $choiceId, $userId, $userRank, $likeness ) {
		$this->dividedUsersInChoices[ $choiceId ]["users"][] = [
			"rank"     => $userRank,
			"id"       => $userId,
			"likeness" => $likeness
		];
	}

	private function removeUserFromChoice( $choiceId, $userId ) {
		$currentChoice = $this->dividedUsersInChoices[ $choiceId ];

		foreach ( $currentChoice['users'] as $key => $user ) {
			if ( $user['id'] == $userId ) {
				$userDetails = $currentChoice['users'][ $key ];
				unset( $currentChoice['users'][ $key ] );
				$this->dividedUsersInChoices[ $choiceId ] = $currentChoice;

				return $userDetails;
			}
		}

		return NULL;
	}

	private function updateChoiceProperties( $choiceId ) {
		$currentChoice = $this->dividedUsersInChoices[ $choiceId ];

		$max           = $currentChoice['max'];
		$users         = $currentChoice['users'];
		$numberOfUsers = count( $users );

		if ( $numberOfUsers == $max ) {
			$this->stopAcceptingUsers( $choiceId );
		} else {
			$this->startAcceptingUsers( $choiceId );
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

	private function startAcceptingUsers( $choiceId ) {
		$this->dividedUsersInChoices[ $choiceId ]['is_accepting'] = true;
	}

	private function stopAcceptingUsers( $choiceId ) {
		$this->dividedUsersInChoices[ $choiceId ]['is_accepting'] = false;
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

	private function getKeyOfUser($userId){
		foreach ($this->usersData as $key=> $user){
			if($userId == $user['user_id']){
				return $key;
			}
		}

		return NULL;
	}

	private function getFreePicksOfUser( $rankOfUser ) {
		$picks     = $this->usersData[ $rankOfUser ]['picks'];
		$freePicks = [];
		foreach ( $picks as $pick ) {
			if ( $pick['can_be_picked'] == true ) {
				$freePicks[] = $pick;
			}
		}

		return $freePicks;
	}

	private function generateNewUserData() {
		$newUserData = [
			'happy' => [],
			'unhappy' => []
		];

		foreach ($this->usersData as $user){
			if($user['divide_status']['user_is_divided'] === false){
				if($user['divide_status']['user_is_happy']){
					$newUserData['happy'][] = $user;
				} else {
					$newUserData['unhappy'][] = $user;
				}
			}
		}

		/** @var Collection $collectionUserData */
		$collectionUserData = collect($newUserData['unhappy'])->merge(collect($newUserData['happy']));

		return $collectionUserData;
	}
}