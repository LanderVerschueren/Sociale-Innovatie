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

class DivideStudent {

	private $elective;
	private $choices;
	private $freeStudents;

	function __construct( Elective $elective ) {
		$this->elective = $elective;
	}

	public function debug_random_pick() {
		$students = User::where( 'is_admin', '0' )->get();

		$choices = $this->elective->choices;

		foreach ( $students as $student ) {
			$picks    = $choices->random( 4 );
			$likeness = 1;
			foreach ( $picks as $pick ) {
				$student->results()->create( [
					"choice_id" => $pick->id,
					"likeness"  => $likeness,
				] );
				$likeness ++;
			}
		}
	}

	public function divide_elective() {
		// Get all results of elective
		$choices = $this->elective->results->sortBy('id');
		$choicesByLikeness = $choices->groupBy( 'likeness' );
		$choicesByUsers = $choices->groupBy('user_id');
		$this->freeStudents = $choicesByUsers->keys();


		dump( $choicesByLikeness );
		dump( $choicesByUsers );
		dump( $this->freeStudents );
	}

}