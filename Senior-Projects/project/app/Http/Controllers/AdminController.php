<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Choice;
use App\ClassGroup;
use App\User;
use App\Elective;
use App\Result;

class AdminController extends Controller
{

    public function login()
    {
        return view('admin.login');
    }

    public function dashboard()
    {
        $electives = Elective::all();
        $name = 'Keuzevakken';
        return view('admin.dashboard')->with(['name' => $name, 'electives' => $electives, 'groups' => '' , 'users' => '', 'choices' => '', 'results' => '']);
    }
    public function showChoicesFromElective($name)
    {
        $elective = Elective::where('name', $name)->first();
    	$choices = Choice::where('elective_id', $elective->id)->get();
        $name = $name;
        return view('admin.dashboard')->with(['name' => $name, 'choices' => $choices, 'users' => '', 'groups' => '', 'electives' => '', 'results' => '']);
    }

    public function showResultsFromChoice($id)
    {
        $results = Result::where('choice_id', $id)->get();
        $choice = Choice::find($id);
        $name = $choice->choice;
        return view('admin.dashboard')->with(['name' => $name, 'results' => $results, 'choices' => '', 'users' => '', 'groups' => '', 'electives' => '']);
    }
}
