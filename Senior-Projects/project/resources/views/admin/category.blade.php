@extends('layouts.app')

@section('content')

<content>
	<h2>Admin</h2>
	<div class="info">
		<ul>
			<li>
			@if($name != null)
				<span class="info">{{ $name }}</span>
			@endif
			</li>
		</ul>
	</div>
	<div class="category">
		@foreach($electives as $elective)
			<div class="card_category">
				<a href="{{ url('/keuzevak/' . $elective->name) }}">{{$elective->name}}</a>
			</div>
	    @endforeach
    </div>
</content>
@endsection