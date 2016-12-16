@extends('layouts.app')

@section('content')

<content>
	<h2>Categorie</h2>
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
    </div>
</content>
@endsection