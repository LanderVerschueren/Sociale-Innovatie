@extends('layouts.app')

@section('content')

<content>
	<h2>Categorie</h2>
	<div class="info">
		<ul>
			<li>
				<span class="label">Naam:</span>
				<span class="info">Naam student</span>
			</li>
			<li>
				<span class="label">E-mailadres:</span>
				<span class="info">E-mailadres student</span>
			</li>
			<li>
				<span class="label">Studentennummer:</span>
				<span class="info">Studentennummer student</span>
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