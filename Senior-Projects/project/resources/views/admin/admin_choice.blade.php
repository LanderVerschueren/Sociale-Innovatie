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
		<button class="button"></button>
	</div>
	<div class="category">
		@foreach($choices as $choice)
			<div class="card_category">
				<a href="{{ url('/keuze/'.$choice->id) }}">{{ $choice->choice }}</a>
			</div>
	    @endforeach
    </div>
</content>
@endsection