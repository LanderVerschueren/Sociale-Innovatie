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

        <p>Kies alstublieft {{$choicesAmount}} vakken.</p>

    <div class="choice">
        <p class="guide">Selecteer {{$choicesAmount}} vakken die je graag zou volgen</p>
        @if($message)
            <p class="message">{{$message}}</p>
        @endif
        <form action="/rightOrder" method="post">
            {{ csrf_field() }}
            <div class="card_container">
                @foreach($choices as $choice)                
                    <div class="card_choice" id="{{ $choice->choice }}">
                        <a class="card_choice_link" href="{{ $choice->choice }}">{{$choice->choice}}</a>
                        <a class="card_choice_info modal-trigger" data-id="{{ $choice->id }}" data-toggle="modal" data-target="#descriptionModal" data-title="{{ $choice->choice }}" data-description="{{ $choice->description }}">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                        </a>
                        <input type="checkbox" name="{{$choice->choice}}" value="{{$choice->id}}">
                    </div>
                @endforeach
            </div>
            <div class="container_button">
                <button type="submit" class="button">Bevestig</button>
            </div>
        </form>
    </div>
</content>

<div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="descriptionModalLabel"></h1>
            </div>
            <div class="modal-body">
                <p id="descriptionModalParagraph"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="button" data-dismiss="modal">Sluiten</button>
            </div>
        </div>
    </div>
</div>
@endsection