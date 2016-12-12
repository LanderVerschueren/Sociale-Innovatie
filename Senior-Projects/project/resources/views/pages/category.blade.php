@extends('layouts.app')

@section('content')
	@foreach($electives as $elective)

                    <p><a href="/{{$elective->id}}/choices">{{$elective->name}}</a></p>

                    @endforeach
@endsection