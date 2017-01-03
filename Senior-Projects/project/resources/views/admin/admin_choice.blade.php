@extends('layouts.app')

@section('content')

<content>
	<h2>Admin</h2>
	<div class="info">
		<ul>
			<li>
			@if($name != null)
				<span class="data">{{ $name }}</span>
			@endif
			</li>
		</ul>
		<button class="button"></button>
	</div>
	<div class="category">

			<div class="card_container">
		@foreach($choices as $choice)
			<!--
			<div class="card_category">
				<a href="{{ url('/keuze/'.$choice->id) }}">{{ $choice->choice }}</a>
			</div>
			-->
				<div class="card_choice" id="{{ $choice->choice }}">
					<a class="card_choice_link" href="{{ $choice->id }}">{{ $choice->choice }}</a>
					<a class="card_choice_info modal-trigger" data-id="{{ $choice->id }}" data-toggle="modal" data-target="#adminChoiceModal">
						<i class="fa fa-info-circle" aria-hidden="true"></i>
					</a>
					<input type="checkbox" name="{{$choice->choice}}" value="{{$choice->id}}">
				</div>
	    @endforeach

			</div>
    </div>
</content>
@endsection