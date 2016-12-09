@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>
                <div class="panel-body">
                    @if($name != null)
                        {{ $name }}
                    @endif
                </div>
                @if($electives != null)
                <ul>
                    @foreach($electives as $elective)
                        <li><a href="{{ url('/keuzevak/'.$elective->name) }}">{{ $elective->name }}</a></li>
                    @endforeach
                </ul>
                @endif
                @if($choices != null)
                <ul>
                    @foreach($choices as $choice)
                        <li><a href="{{ url('/keuze/'.$choice->id) }}">{{ $choice->choice }}</a></li>
                    @endforeach
                </ul>
                @endif
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
