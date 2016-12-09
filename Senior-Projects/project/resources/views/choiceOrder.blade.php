
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <h1> Order Choices </h1>

            @foreach($choices as $choice)

                <p>keuze1</p>
                <p>{{$choice->choice}}</p>
                @endforeach

        </div>
    </div>
@endsection
