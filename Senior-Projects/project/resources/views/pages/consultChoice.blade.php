@extends('layouts.app')

@section('content')

<content>
    <header>
        <h2>Keuze</h2>        
        <form id="logout-form" action="/logout" method="POST">
            {{ csrf_field() }}
            <button type="submit" class="button">Uitloggen</button>
        </form>
    </header>
    <div class="info">
        <ul>
            <li>
                <span class="label">Naam:</span>
                <span class="data">{{Auth::user()->first_name}} {{Auth::user()->surname}}</span>
            </li>
            <li>
                <span class="label">E-mailadres:</span>
                <span class="data">{{Auth::user()->email}}</span>
            </li>
            <li>
                <span class="label">Studentennummer:</span>
                <span class="data">{{Auth::user()->student_id}}</span>
            </li>
        </ul>
    </div>
    <div class="choice">
        <p class="guide">Hier zie je al je gemaakte keuzes voor {{$elective->name}}</p>               
	            @foreach($resultsForElective as $choice)
                    	<div class="card_choice_order">
                            <p class="card_choice_order_link">{{$choice["name"]}}</p>
	                    </div>
				@endforeach  
    </div>
</content>

@endsection