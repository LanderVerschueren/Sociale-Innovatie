<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Elective;
use App\ClassGroup;
use App\Choice;
use App\Result;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {

        //De electives van de student ophalen.
        //Het ophalen gaat via een paar tables. Eerst de classgroup, via de classgroup naar de choice_class_group.
        //Aan de hand van de Choice_class_group gaan we naar de choices.
        //Via de choices kunnen we aan de electives komen.
        //De electives worden opgeslagen in een array, enkel als de datum tussen de start en eind datum van deze elective zit.
        //En wanneer de user nog geen result heeft opgeslagen van deze elective.

        //deleten actieve sessie
        $elective_id = $request->session()->pull('active_elective');

        $class_group_id = Auth::user()->class_group_id;
        $choice_class_groups = DB::table('choice_class_group')->where('class_group_id', $class_group_id)->get();
        $electiveIds = [];

        foreach($choice_class_groups as $choice_class_group)
        {
            $choice = Choice::where('id', $choice_class_group->choice_id)->first();
            array_push($electiveIds, $choice->elective_id);
        }

        $uniqueElectivesId = array_unique ( $electiveIds );

        $electives = [];
        $passiveElectives = [];

        foreach ($uniqueElectivesId as $id)
        {
            $elective = Elective::where('id', $id)->first();
            debug($elective->name);
            $thisDate = date("Y-m-d G:i:s");
            $beginDate = $elective->start_date;
            $endDate = $elective->end_date;
            if(($thisDate<=$endDate) && ($thisDate>=$beginDate))
            {
                if(Auth::user()->canAnswer($elective))
                {
                    array_push($electives, $elective);
                }
                else
                {
                    array_push($passiveElectives, $elective);
                }
            }
        }

        $message = $request->session()->get('status');

        return view('pages.category', compact('electives', 'passiveElectives' , 'message'));
    }

    public function choices(Elective $elective, Request $request)
    {
        //Al de keuzes van de geslecteerde Elective tonen.
        //Sessie wordt aangemaakt met de elective id in voor fraude te voorkomen.
        $choicesAmount = $elective->choicesAmount;
        if(Auth::user()->canAnswer($elective))
        {
            $request->session()->put('active_elective', $elective->id);

            $class_group_id = Auth::user()->class_group_id;
            $choice_class_groups = DB::table('choice_class_group')->where('class_group_id', $class_group_id)->get();
            $choices = [];
            foreach ($choice_class_groups as $choice_class_group)
            {
                $newChoice = Choice::where('id', $choice_class_group->choice_id)->first();
                if($newChoice->elective_id == $elective->id)
                {
                    array_push($choices, $newChoice);
                }
            }
            $message = $request->session()->get('status');
            return view('pages.choice', compact('choices', 'choicesAmount', 'message'));
        }
        else{
            $request->session()->flash('status', 'Je mag geen resultaten doorsturen voor dit keuzevak.');
            return redirect("/category");
        }

    }

    public function consultCoices(Elective $elective, Request $request)
    {
        $user = Auth::user()->id;
        $optionalChoices = Choice::where("elective_id", $elective->id)->get();
        $results = Result::where("user_id", $user)->get();
        $resultsForElective = [];
        foreach ($optionalChoices as $choice){
            foreach ($results as $result){
                if($result->choice_id == $choice->id){
                    $newResult["name"] = $choice->choice;
                    $newResult["likeness"] = $result->likeness;
                    array_push($resultsForElective, $newResult);
                }
            }
        }
        //return $resultsForElective;
        $sortedResultsForElective = [];

        $startNumber = 1;

        for($count = 1; $count<=count($resultsForElective);  $count++)
        {
            foreach ($resultsForElective as $sortResult){

                if($sortResult["likeness"] == $startNumber){
                    array_push($sortedResultsForElective, $sortResult);
                }
            }
            $startNumber++;
        }

        $resultsForElective = $sortedResultsForElective;

        return view("pages.consultChoice", compact("resultsForElective", "elective"));

    }

    public function store_choice(Request $request)
    {
        //De 6 keuzes die gemaakt zijn doorgegeven met een post. Er wordt gecheckt of er 6 zijn aangeduid
        //Deze 6 keuzes worden meegegeven aan de volgende pagina en daar worden ze getoont om een likeness mee te geven
        //Er wordt gecheckt of de active_elective sessie bestaat. Zo niet wordt de gebruiker terug naar de homepagina doorgestuurd.
        //Uit de sessie wordt de acrive elective gehaald om zo het aantal keuzes te weten.

         if ($request->session()->has('active_elective')) {

            $elective_id = $request->session()->get('active_elective');
            $active_elective = Elective::where("id", $elective_id)->first();
            $choiceIds = [];
            $choices = [];
            $choice_counter = $active_elective->choicesAmount;
            foreach ($request->request as $choice => $id) {
                if ($choice != "_token") {
                    if ($choice_counter) {
                        array_push($choiceIds, $id);
                        $choice_counter--;
                    } else {
                        $request->session()->flash('status', 'Foute hoeveelheid vakken aangeduid');
                        return back()->withInput();
                    }
                }
            }

            if ($choice_counter) {
                $request->session()->flash('status', 'Foute hoeveelheid vakken aangeduid');
                return back()->withInput();
            }

            foreach ($choiceIds as $choice) {
                $choiceObject = Choice::where('id', $choice)->first();
                array_push($choices, $choiceObject);
            }

            return view("pages.choiceOrder", compact('choices'));
        }
        else{
            $request->session()->flash('status', 'Je sessie is verlopen. Probeer het opnieuw');
            return redirect("/category");
        }
    }

    public function store_order(Request $request)
    {

        // Hier worden de results opgeslagen.
        // Per result worde de likeness ook opgeslage.
        // Eerst wordt gecheckt of er geen dubbele waardes zijn opgeslagen.
        // Eerst wordt gecontroleerd of de active_elective sessie bestaat.
        if ($request->session()->has('active_elective')) {
            $elective_id = $request->session()->pull('active_elective');
            $active_elective = Elective::where('id', $elective_id)->first();
            $choicesAmount = $active_elective->choicesAmount;
            $input = $request->request->all();

            $choices = $input["choice"];

            $amount = count($choices);

            if ($amount != $choicesAmount) {
                $request->session()->flash('status', 'Er is iets fout gegaan.');
                return redirect("/category");
            }

            $likeness = 0;

            foreach ($choices as $choice) {

                $newResult = Result::where([['choice_id', $choice], ['user_id', Auth::user()->id]])->first();
                if ($newResult) {
                    $request->session()->flash('status', 'Je hebt je keuze al doorgestuurd.');
                    return redirect("/category");
                }

            }

            foreach ($choices as $choice) {

                if ($likeness < ($choicesAmount-1)) {
                    $likeness++;
                    $result = new Result;
                    $result->choice_id = $choice;
                    $result->likeness = $likeness;
                    Auth::user()->results()->save($result);
                }
            }

            $request->session()->flash('status', "Je keuze is geregistreerd.");
            return redirect("/category");
        }else{
            $request->session()->flash('status', 'Je sessie is verlopen. Probeer het opnieuw');
            return redirect("/category");
        }
    }

}