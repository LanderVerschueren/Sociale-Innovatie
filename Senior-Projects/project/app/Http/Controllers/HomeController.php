<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Elective;
use App\ClassGroup;
use App\Choice;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function choices(Elective $elective)
    {
        $choices = Choice::where('elective_id', $elective->id)->get();

        return view("choices", compact('choices'));
    }

    public function index()
    {
        $class_group_id = Auth::user()->class_group_id;
        $choice_class_group = DB::table('choice_class_group')->where('class_group_id', $class_group_id)->get();

        $counter = 0;
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
            array_push($electives, $elective);
        }

        return view('home', compact('electives'));
    }
}
