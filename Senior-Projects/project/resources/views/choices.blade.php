
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <h1>Choices</h1>


            <form action="/category" method="post">
                {{ csrf_field() }}
                    @foreach($choices as $choice)

                    <input type="checkbox" name="{{$choice->choice}}" value="{{$choice->id}}"> {{$choice->choice}} </br>

                    @endforeach

                <input type="submit" value="verstuur">
            </form>


        </div>
    </div>
@endsection
