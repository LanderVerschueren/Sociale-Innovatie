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

        foreach ($uniqueElectivesId as $id)
        {
            $elective = Elective::where('id', $id)->first();
            debug($elective->name);
            $thisDate = date("Y-m-d G:i:s");
            $beginDate = $elective->start_date;
            $endDate = $elective->end_date;
            if(($thisDate<=$endDate) && ($thisDate>=$beginDate))
            {
                if(Auth::user()->hasNoResult($elective))
                {
                    array_push($electives, $elective);
                }
            }
        }

        $message = $request->session()->get('status');

        return view('pages.category', compact('electives', 'message'));
    }

    public function choices(Elective $elective, Request $request)
    {
        //Al de keuzes van de geslecteerde Elective tonen.
        if(Auth::user()->hasNoResult($elective))
        {
            $choices = Choice::where('elective_id', $elective->id)->get();
            $message = $request->session()->get('status');
            return view('pages.choice', compact('choices', 'message'));
        }
        else{
            $request->session()->flash('status', 'Je hebt je resultaten hiervoor al doorgestuurd.');
            return redirect("/category");
        }

    }

    public function store_choice(Request $request)
    {
        //De 6 keuzes die gemaakt zijn doorgegeven met een post. Er wordt gecheckt of er 6 zijn aangeduid
        //Deze 6 keuzes worden meegegeven aan de volgende pagina en daar worden ze getoont om een likeness mee te geven

        $choiceIds = [];
        $choices = [];
        $choice_counter = 6;
        foreach ($request->request as $choice => $id)
        {
            if($choice != "_token")
            {
                if($choice_counter)
                {
                    array_push($choiceIds, $id);
                    $choice_counter--;
                }
                else
                {
                    $request->session()->flash('status', 'Je moet 6 vakken aanduiden!');
                    return back()->withInput();
                }
            }
        }

        if($choice_counter)
        {
            return back()->withInput();
        }

        foreach ($choiceIds as $choice)
        {
            $choiceObject = Choice::where('id', $choice)->first();
            array_push($choices, $choiceObject);
        }

        return view("pages.choiceOrder", compact('choices'));
    }

    public function store_order(Request $request)
    {

        // Hier worden de results opgeslagen.
        // Per result worde de likeness ook opgeslage.
        // Eerst wordt gecheckt of er geen dubbele waardes zijn opgeslagen.

        $input = $request->request->all();

        $choices = $input["choice"];

        $amount = count($choices);

        if($amount != 6)
        {
            $request->session()->flash('status', 'Er is iets fout gegaan.');
            return redirect("/category");
        }

        $likeness = 0;

        foreach ($choices as $choice)
        {

            $newResult = Result::where([['choice_id', $choice], ['user_id', Auth::user()->id]])->first();
            if($newResult)
            {
                $request->session()->flash('status', 'Er is iets fout gegaan.');
                return redirect("/category");
            }

        }

        foreach ($choices as $choice) {

            if($likeness < 5)
            {
                $likeness++;
                $result = new Result;
                $result->choice_id = $choice;
                $result->likeness = $likeness;
                Auth::user()->results()->save($result);
            }
        }

        return redirect("/category");
    }
}