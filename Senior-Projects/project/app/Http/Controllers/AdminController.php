<?php

namespace App\Http\Controllers;

use App\Klas;
use App\Services\DivideStudent;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Choice;
use App\ClassGroup;
use App\User;
use App\Elective;
use App\Result;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;
use Session;

class AdminController extends Controller {

	public function login() {
		return view( 'admin.login' );
	}

	public function dashboard() {
		$electives = Elective::all();
		$name      = 'Keuzevakken';

		return view( 'admin.admin_category' )->with( [
			'name'      => $name,
			'electives' => $electives
		] );
	}

	public function showChoicesFromElective( $name ) {
		$elective            = Elective::where( 'name', $name )->first();
		$choices             = Choice::where( 'elective_id', $elective->id )->get();
		$electiveName        = $name;
		$classes             = Klas::all();
		$amounts             = DB::table( 'elective_class_amount' )->where( 'elective_id', $elective->id )->get();
		$choice_class_groups = DB::table( 'choice_class_group' )->get();
		$class_groups        = ClassGroup::all();

		return view( 'admin.admin_choice' )->with( [
			'choices'             => $choices,
			'elective'            => $elective,
			'classes'             => $classes,
			'amounts'             => $amounts,
			'classgroups'         => $class_groups,
			'choice_class_groups' => $choice_class_groups
		] );
	}

	public function showResultsFromChoice( $id ) {
		$results = Result::where( 'choice_id', $id )->get();
		$choice  = Choice::find( $id );
		$name    = 'Resultaten';

		return view( 'admin.admin_results' )->with( [
			'name'    => $name,
			'results' => $results
		] );
	}

	public function addElective( Request $request ) {

		$this->validate( $request, [
			'name'       => 'required',
			'test_date'  => 'required',
			'start_date' => 'required',
			'end_date'   => 'required'
		] );

		$elective = new Elective;

		$elective->name       = $request->name;
		$elective->test_date  = $request->test_date;
		$elective->start_date = $request->start_date;
		$elective->end_date   = $request->end_date;

		$elective->save();

		foreach ( Klas::all() as $class ) {
			DB::table( 'elective_class_amount' )->insert( [
				'elective_id' => $elective->id,
				'class_id'    => $class->id,
				'amount'      => 0
			] );
		}

		$electives = Elective::all();
		$name      = 'Keuzevakken';

		return view( 'admin.admin_category' )->with( [
			'name'      => $name,
			'electives' => $electives
		] );
	}

	public function editElective( $name ) {
		$elective = Elective::where( 'name', $name )->first();

		if ( ! $elective ) {
			abort( 404 );
		}

		return view( 'admin.elective.edit' )->with( 'elective', $elective );
	}

	public function updateElective( Request $request, $id ) {
		$this->validate( $request, [
			'name'       => 'required',
			'test_date'  => 'required',
			'start_date' => 'required',
			'end_date'   => 'required'
		] );
		$elective = Elective::where( 'id', $id )->first();

		$elective->name       = $request->name;
		$elective->test_date  = $request->test_date;
		$elective->start_date = $request->start_date;
		$elective->end_date   = $request->end_date;

		$elective->save();
		$electives = Elective::all();

		$name = 'Keuzevakken';

		return view( 'admin.dashboard' )->with( [
			'name'      => $name,
			'electives' => $electives,
		] );
	}

	public function addChoiceToElective( Request $request, $name ) {

		$this->validate( $request, [
			'choice'      => 'required',
			'description' => 'required',
			'minimum'     => 'required|integer',
			'maximum'     => 'required|integer',
		] );

		$elective = Elective::where( 'name', $name )->first();

		$choice = new Choice;

		$choice->choice      = $request->choice;
		$choice->description = $request->description;
		$choice->minimum     = $request->minimum;
		$choice->maximum     = $request->maximum;
		$choice->elective_id = $elective->id;

		$choice->save();

		foreach ( $request->get( 'group' ) as $group ) {
			DB::table( 'choice_class_group' )->insert( [
				'choice_id'      => $choice->id,
				'class_group_id' => $group
			] );
		}

		$choices = Choice::where( 'elective_id', $elective->id )->get();

		return redirect( '/keuzevak/' . $elective->name )->with( [
			'name'    => $elective->name,
			'choices' => $choices,
		] );
	}

	public function updateChoice( Request $request, $name ) {

		$this->validate( $request, [
			'choice'      => 'required',
			'description' => 'required',
			'minimum'     => 'required|integer',
			'maximum'     => 'required|integer',
		] );

		$elective = Elective::where( 'name', $name )->first();

		$choice = new Choice;

		$choice->choice      = $request->choice;
		$choice->description = $request->description;
		$choice->minimum     = $request->minimum;
		$choice->maximum     = $request->maximum;
		$choice->elective_id = $elective->id;

		$choice->save();

		foreach ( $request->get( 'group' ) as $group ) {
			DB::table( 'choice_class_group' )->insert( [
				'choice_id'      => $choice->id,
				'class_group_id' => $group
			] );
		}

		$choices = Choice::where( 'elective_id', $elective->id )->get();

		return redirect( '/keuzevak/' . $elective->name )->with( [
			'name'    => $elective->name,
			'choices' => $choices,
		] );
	}

	public function deleteChoice( Request $request, $id ) {
		$choice = Choice::whereId( $id );
		$choice->delete();

		return back();
	}

	public function divideElective( $electiveId ) {
		$elective       = Elective::find( $electiveId );
		$divideProvider = new DivideStudent( $elective );
		/** @var LaravelExcelWriter $excel */
		$excel = Excel::create( $elective->name, function ( $excel ) use ( $elective, $divideProvider ) {
			/** @var LaravelExcelWriter $excel */
			$userArray = $divideProvider->divide_elective();

			$excel->sheet( 'verdeelde_keuzes', function ( $sheet ) use ( $userArray ) {
				/** @var LaravelExcelWorksheet $sheet */
				/** @var array $userArray */
				$data = [];

				$maxChoices = 0;

				foreach ( $userArray as $user ) {
					$maxChoices = ( $maxChoices < $user['number_of_choices'] ? $user['number_of_choices'] : $maxChoices );
				}

				foreach ( $userArray as $user ) {
					$dataOfUser = [];

					$dividedCount = 1;
					foreach ( $user['picks'] as $pick ) {
						if ( ! $pick['can_be_picked'] ) {
							$dataOfUser[ 'Keuze ' . $dividedCount ] = sprintf('%s (%d)', $pick['name'], $pick['rank']);
							$dividedCount ++;
						}
					}

					for ( $rest = $dividedCount; $rest <= $maxChoices; $rest ++ ) {
						$dataOfUser[ 'Keuze ' . $rest ] = NULL;
					}

					$dataOfUser = array_merge( [
						'Studenten ID'             => $user['school_id'],
						'Aantal keuzes'            => $user['number_of_choices'],
						'Student is verdeeld'      => $user['divide_status']['user_is_divided'] ? 'Ja' : 'Neen',
						'Student is gelukkig'      => $user['divide_status']['user_is_happy'] ? 'Ja' : 'Neen',
						'Student\'s hoogste keuze' => $user['divide_status']['divide_likeness'],
					], $dataOfUser );

					$data[] = $dataOfUser;
				}

				$sheet->freezeFirstRow();
				$sheet->with( $data );
			} );

			$excel->sheet( 'gekozen_keuzes', function ( $sheet ) use ( $userArray, $elective ) {
				/** @var LaravelExcelWorksheet $sheet */
				/** @var array $userArray */

				$choices = $elective->choices;

				$choicesArray = [];
				foreach ( $choices as $choice ) {
					$choicesArray[ $choice->choice ] = NULL;
				}

				$data = [];

				foreach ( $userArray as $user ) {
					$dataOfUser = $choicesArray;

					foreach ( $user['picks'] as $pick ) {
						$dataOfUser[ $pick['name'] ] = $pick['rank'];
					}

					$dataOfUser = array_merge( [
						'Studenten ID'  => $user['school_id'],
						'Aantal keuzes' => $user['number_of_choices']
					], $dataOfUser );

					$data[] = $dataOfUser;
				}

				$sheet->freezeFirstRow();
				$sheet->with( $data );
			} );

			$excel->sheet( 'studenten', function ( $sheet ) use ( $userArray ) {
				/** @var LaravelExcelWorksheet $sheet */
				/** @var array $userArray */

				$students = [];

				foreach ( $userArray as $user ) {
					/** @var User $student */
					$student = User::where( 'student_id', $user['school_id'] )->get( [
						'class_group_id',
						'student_id',
						'first_name',
						'surname',
						'email'
					] )->first();

					$students[] = [
						'Studenten ID' => $student->student_id,
						'Voornaam'     => $student->first_name,
						'Achternaam'   => $student->surname,
						'E-mail'       => $student->email,
						'Klas'         => $student->class_group->class_group,
						'Klasgroep'    => $student->class_group->classes->abbreviation
					];
				}

				$sheet->freezeFirstRow();
				$sheet->with( $students );
			} );
		} );
		$excel->download( 'xlsx' );
	}

	public function giveAmountToClasses( Request $request, $id ) {
		$elective = Elective::where( 'id', $id )->first();
		$choices  = Choice::where( 'elective_id', $elective->id )->get();
		$classes  = Klas::all();
		$counter  = 0;


		foreach ( $request->get( 'number' ) as $number ) {

			if ( DB::table( 'elective_class_amount' )->where( [
				[ 'elective_id', $id ],
				[ 'class_id', $classes[ $counter ]->id ]
			] )->get()
			) {

				DB::table( 'elective_class_amount' )
				  ->where( [
					  [ 'elective_id', $id ],
					  [ 'class_id', $classes[ $counter ]->id ]
				  ] )->update( [ 'amount' => $number ] );
			} else {
				DB::table( 'elective_class_amount' )->insert( [
					'elective_id' => $id,
					'class_id'    => $classes[ $counter ]->id,
					'amount'      => $number
				] );
			}


			$counter += 1;
		}

		return redirect( '/keuzevak/' . $elective->name )->with( [
			'name'    => $elective->name,
			'choices' => $choices
		] );
	}


	public function getImportStudents() {
		return view( 'admin.import' );
	}

	public function postImportStudents( Request $request ) {
		dump( $request );

		$studentCollection = collect();
		Excel::load( $request->file( 'import_excel' ) )->each( function ( Collection $line ) use ( $studentCollection ) {
			$line = $line->toArray();
			$studentCollection->push( $line );
		} );

		dump( $studentCollection );

		$followedLessonsPerSubGroup = $studentCollection->groupBy( 'subgroep' );
		$followedLessonsPerClasses  = $studentCollection->groupBy( 'klasgroep' );

		debug( $followedLessonsPerClasses );
		// Make classes
		$classes      = [];
		$class_groups = [];
		foreach ( $studentCollection as $student ) {
			// Get or create the class
			$class_id = array_search( $student["klasgroep"], $classes );
			if ( $class_id === false ) {
				$class                 = Klas::firstOrCreate( [
					"class"        => $student["klasgroep"],
					"abbreviation" => $student["afkorting"],
				] );
				$classes[ $class->id ] = $student["klasgroep"];
				$class_id              = $class->id;
			}

			// Get or create the sub group
			$class_group_id = array_search( $student["subgroep"], $class_groups );
			if ( $class_group_id === false ) {
				$class_group                        = ClassGroup::firstOrCreate( [
					"class_id"    => $class_id,
					"class_group" => $student["subgroep"],
					"year"        => substr( $student["subgroep"], 3, 1 ),
				] );
				$class_group_id[ $class_group->id ] = $student["subgroep"];
				$class_group_id                     = $class_group->id;
			}

			// Get or create the student
			$student_name = explode( ' ', $student["student"] );

			$student_first_name = $student_name[ count( $student_name ) - 1 ];
			array_pop( $student_name );
			$student_surname = implode( " ", $student_name );

			$student = User::firstOrCreate( [
				"surname"        => $student_surname,
				"first_name"     => $student_first_name,
				"email"          => $student["school_email"],
				"student_id"     => $student["registratienummer"],
				"class_group_id" => $class_group_id
			] );
		}

		dump( $followedLessonsPerSubGroup );
		dump( $followedLessonsPerClasses );

		dump( 'end' );
	}
}
