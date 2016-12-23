@extends('layouts.app')

@section('content')

<content>
    <h2>Keuze</h2>
    <div class="info">
        <ul>
            <li>
                <span class="label">Naam:</span>
                <span class="info">Naam student</span>
            </li>
            <li>
                <span class="label">E-mailadres:</span>
                <span class="info">E-mailadres student</span>
            </li>
            <li>
                <span class="label">Studentennummer:</span>
                <span class="info">Studentennummer student</span>
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
                        <a class="card_choice_info">
                            <i class="fa fa-info-circle" id="{{ $choice->id }}" aria-hidden="true"></i>
                        </a>
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