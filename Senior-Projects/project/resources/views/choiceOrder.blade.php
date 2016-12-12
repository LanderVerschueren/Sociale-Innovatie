
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">



            <h1> gemaakte keuzes (tijdelijke pagina) </h1>

            @foreach($choices as $choice)


                <p>{{$choice->choice}}</p>
                @endforeach

        </div>
    </div>
@endsection
