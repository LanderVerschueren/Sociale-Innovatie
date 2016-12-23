@extends('layouts.app')

@section('content')

<content>
    @if($message)
        <p>{{$message}}</p>
    @endif
    <h2>Keuze</h2>
    <div class="info">
        <ul>
            <li>
                <span class="label">Naam:</span>
                <span class="info">{{Auth::user()->first_name}} {{Auth::user()->surname}}</span>
            </li>
            <li>
                <span class="label">E-mailadres:</span>
                <span class="info">{{Auth::user()->email}}</span>
            </li>
            <li>
                <span class="label">Studentennummer:</span>
                <span class="info">{{Auth::user()->student_id}}</span>
            </li>
        </ul>
    </div>
    <div class="choice">
        <form action="/rightOrder" method="post">
            {{ csrf_field() }}
            <div>
                @foreach($choices as $choice)                
                    <div class="card_choice" id="{{ $choice->choice }}">
                        <a class="card_choice_link" href="{{ $choice->choice }}">{{$choice->choice}}</a>
                        <input type="checkbox" name="{{$choice->choice}}" value="{{$choice->id}}">
                    </div>
                @endforeach
            </div>
            <div class="container_button">
                <button type="submit">Bevestig</button>
            </div>
        </form>
    </div>
</content>
@endsection