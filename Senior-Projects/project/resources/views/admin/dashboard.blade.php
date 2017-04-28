@extends('layouts.app')

@section('content')
<div class="container">
    azerazetzerhdh r
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
                <div class="panel-body">
                    @if($name != null)
                        {{ $name }}
                    @endif
                </div>
                <!-- oplijsting van keuzevakken met knop om een keuzevak toe te voegen -->
                @if($electives != null)
                <form method="POST" action="{{ url('/addElective') }}">
                    {{ csrf_field() }}
                    <label for="name">Naam keuzevak: </label><input type="text" name="name">
                    <label for="start_date">Begindatum: </label><input type="date" name="start_date">
                    <label for="end_date">Einddatum: </label><input type="date" name="end_date">
                    <button type="submit" class="btn btn-primary">Voeg keuzevak toe</button>
                </form>
                <ul>
                    @foreach($electives as $elective)
                        <li><a href="{{ url('/keuzevak/'.$elective->name) }}">{{ $elective->name }}</a></li>
                    @endforeach
                </ul>
                @endif
                <!-- lijst van keuzes met van de keuzevakken-->
                @if($choices != null)
                <form method="POST" action="{{ url('/addChoice/'.$name) }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="{{ $name }}">
                    <label for="choice">Naam keuze:</label><input type="text" name="choice">
                    <label for="description">Beschrijving:</label><input type="textarea" name="description">
                    <label for="minimum">Min. aantal mensen:</label><input type="number" name="minimum">
                    <label for="maximum">Max. aantal mensen:</label><input type="number" name="maximum">
                    <button type="submit" class="btn btn-primary">Voeg keuze toe</button>
                </form>
                <ul>
                    @foreach($choices as $choice)
                        <li><a href="{{ url('/keuze/'.$choice->id) }}">{{ $choice->choice }}</a></li>
                    @endforeach
                </ul>
                @endif
                <!-- oplijsting van de resultaten van een bepaalde keuze -->
                @if($results != null)
                <ul>
                    @foreach($results as $result)
                        <li>{{ $result->users()->first()->first_name}} {{ $result->users()->first()->surname}} {{ $result->likeness}}</li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
