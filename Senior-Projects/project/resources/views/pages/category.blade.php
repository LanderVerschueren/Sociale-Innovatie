@extends('layouts.app')

@section('content')

<content>

	<form id="logout-form" action="/logout" method="POST">
		{{ csrf_field() }}
		<input type="submit" value="logout" class="button">
	</form>

	@if($message)
		<p>{{$message}}</p>
	@endif
	<h2>Keuzevakken</h2>
	<div class="info">
		<ul>
			<li>
				<span class="label">Naam:</span>
				<span class="info">{{Auth::user()->first_name}} {{Auth::user()->surname}}</span>
			</li>
			<li>
				<span class="label">E-mailadres:</span>
				<span class="info">{{Auth::user()->email}}</span>
			</li>
			<li>
				<span class="label">Studentennummer:</span>
				<span class="info">{{Auth::user()->student_id}}</span>
			</li>
		</ul>
	</div>
	<div class="category">
		@foreach($electives as $elective)
			<div class="card_category">
				<a href="/{{$elective->id}}/choices">{{$elective->name}}</a>
			</div>
	    @endforeach

		@if($passiveElectives)
		<h2>Passive Electives</h2>
		@foreach($passiveElectives as $elective)
				<div class="card_category">
					<a href="/{{$elective->id}}/choices">{{$elective->name}}</a>
				</div>
			@endforeach
			@endif
    </div>
</content>
@endsection