
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">



            <h1> gemaakte keuzes (tijdelijke pagina) </h1>

            <form action="/storeOrder" method="post">
                {{ csrf_field() }}
                @foreach($choices as $choice)

                    <p>{{$choice->choice}}</p>

                    <input type="number" name="{{$choice->id}}">

                @endforeach

                    <input type="submit" value="verstuur keuze">
            </form>


        </div>
    </div>
@endsection
