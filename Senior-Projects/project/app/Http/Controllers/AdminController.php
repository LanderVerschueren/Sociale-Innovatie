<?php

namespace App\Http\Controllers;

use App\Klas;
use Illuminate\Http\Request;
use App\Choice;
use App\ClassGroup;
use App\User;
use App\Elective;
use App\Result;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Session;

class AdminController extends Controller {

	public function login($request) {
		session()->flash('test', 'test');
		return redirect('/');
	}

	public function dashboard() {
		$electives = Elective::all();
		$name      = 'Keuzevakken';

		return view( 'admin.admin_category' )->with( [
			'name'      => $name,
			'electives' => $electives,
			'groups'    => '',
			'users'     => '',
			'choices'   => '',
			'results'   => ''
		] );
	}

	public function showChoicesFromElective( $name ) {
		$elective = Elective::where( 'name', $name )->first();
		$choices  = Choice::where( 'elective_id', $elective->id )->get();
		$name     = $name;

		return view( 'admin.admin_choice' )->with( [
			'name'      => $name,
			'choices'   => $choices,
			'users'     => '',
			'groups'    => '',
			'electives' => '',
			'results'   => ''
		] );
	}

	public function showResultsFromChoice( $id ) {
		$results = Result::where( 'choice_id', $id )->get();
		$choice  = Choice::find( $id );
		$name    = 'Resultaten';

		return view( 'admin.admin_results' )->with( [
			'name'      => $name,
			'results'   => $results,
			'choices'   => '',
			'users'     => '',
			'groups'    => '',
			'electives' => ''
		] );
	}

	public function addElective( Request $request){

		$this->validate($request,[
            'name'=> 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);

		$elective = new Elective;

		$elective->name = $request->name;
		$elective->start_date = $request->start_date;
 		$elective->end_date = $request->end_date;

 		$elective->save();

 		$electives = Elective::all();
 		$name = 'Keuzevakken';
 		return view( 'admin.admin_category' )->with( [
			'name'      => $name,
			'electives' => $electives,
			'groups'    => '',
			'users'     => '',
			'choices'   => '',
			'results'   => ''
		] );
	}

	public function editElective($name)
    {
        $elective = Elective::where( 'name', $name )->first();

        if(!$elective){
          abort(404);
        }
        return view('admin.elective.edit')->with('elective',$elective);
    }

    public function updateElective(Request $request, $id)
    {
      $this->validate($request,[
            'name'=> 'required',
            'start_date' => 'required',
            'end_date' => 'required'
        ]);
      
		$elective->name = $request->name;
		$elective->start_date = $request->start_date;
 		$elective->end_date = $request->end_date;

 		$elective->save();
	      return view( 'admin.dashboard' )->with( [
				'name'      => $name,
				'electives' => $electives,
				'groups'    => '',
				'users'     => '',
				'choices'   => '',
				'results'   => ''
			] );
    }

	public function addChoiceToElective( Request $request, $name){

		$this->validate($request,[
            'choice'=> 'required',
            'description' => 'required',
            'minimum' => 'required|integer',
            'maximum' => 'required|integer',
        ]);

		$elective = Elective::where( 'name', $name )->first();
		$choices = Choice::where( 'elective_id', $elective->id )->get();
		$choice = new Choice;

		$choice->choice = $request->choice;
		$choice->description = $request->description;
		$choice->minimum = $request->minimum;
		$choice->maximum = $request->maximum;
		$choice->elective_id = $elective->id;

		$choice->save();
		return view( 'admin.dashboard' )->with( [
			'name'      => $name,
			'electives' => '',
			'groups'    => '',
			'users'     => '',
			'choices'   => $choices,
			'results'   => ''
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

			$student         = User::firstOrCreate( [
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
