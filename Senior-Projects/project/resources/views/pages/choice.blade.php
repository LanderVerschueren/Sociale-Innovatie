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
        <form action="" method="post">
            {{ csrf_field() }}
            @foreach($choices as $choice)                
                <div class="card_choice">
                    {{$choice->choice}}
                    <input type="checkbox" name="{{$choice->choice}}" value="{{$choice->id}}">
                </div>
            @endforeach
            <input type="submit" value="verstuur">
        </form>
    </div>
</content>
@endsection