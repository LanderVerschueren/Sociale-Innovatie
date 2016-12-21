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

    public function index() {

        //De electives van de student ophalen.
        //Het ophalen gaat via een paar tables. Eerst de classgroup, via de classgroup naar de choice_class_group.
        //Aan de hand van de Choice_class_group gaan we naar de choices.
        //Via de choices kunnen we aan de electives komen.
        //De electives worden opgeslagen in een array, enkel als de datum tussen de start en eind datum van deze elective zit.
        //En wanneer de user nog geen result heeft opgeslagen van deze elective.

        $class_group_id = Auth::user()->class_group_id;
        $choice_class_group = DB::table('choice_class_group')->where('class_group_id', $class_group_id)->get();
        $electiveIds = [];

        foreach($choice_class_group as $choice_class_group)
        {
            $choice = Choice::where('id', $choice_class_group->choice_id)->first();
            array_push($electiveIds, $choice->elective_id);
        }

        $uniqueElectivesId = array_unique ( $electiveIds );

        $electives = [];

        foreach ($uniqueElectivesId as $id)
        {
            $elective = Elective::where('id', $id)->first();
            $thisDate = date("Y-m-d G:i:s");
            //$thisDate = '2016-12-08 12:12:12';
            debug($thisDate);
            $beginDate = $elective->start_date;
            $endDate = $elective->end_date;
            debug($thisDate . "  " . $endDate);
            if(($thisDate<=$endDate) && ($thisDate>=$beginDate))
            {
                if(Auth::user()->hasNoResult($elective))
                {
                    array_push($electives, $elective);
                }
            }
        }

        return view('pages.category', compact('electives'));
    }




    public function choices(Elective $elective)
    {
        //Al de keuzes van de geslecteerde Elective tonen.

        $choices = Choice::where('elective_id', $elective->id)->get();

        return view("choices", compact('choices'));

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

        return view("choiceOrder", compact('choices'));
    }




    public function store_order(Request $request)
    {
        // Hier worden de results opgeslagen.
        // Per result worde de likeness ook opgeslage.
        // Eerst wordt gecheckt of er geen dubbele waardes zijn opgeslagen.

        $useAbleValues = ["1","2","3","4","5","6"];

        foreach ($request->request as $choice => $likeness) {
            if($choice != "_token") {
                if (in_array($likeness, $useAbleValues)) {
                    $key = array_search($likeness, $useAbleValues);
                    unset($useAbleValues[$key]);
                } else {
                    debug($likeness);
                    debug($useAbleValues);
                    return "foute waarden";

                }
            }
        }

        if($useAbleValues)
        {
            return "foute waarden";
        }
        $counter = 5;

        foreach ($request->request as $choice => $likeness)
        {
            if($choice != "_token")
            {
                if($likeness != "6")
                {
                    if($counter)
                    {
                            $result = new Result;
                            $result->choice_id = $choice;
                            $result->likeness = $likeness;
                            Auth::user()->results()->save($result);
                            $counter--;
                    }
                    else{
                        return redirect("/category");
                    }
                }

            }
        }
        return redirect("/category");
    }





}
