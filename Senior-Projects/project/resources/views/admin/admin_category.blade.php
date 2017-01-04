@extends('layouts.app')

@section('content')

<content>
	<header>
		<h2>Admin</h2>
		<button type="submit" class="button modal-trigger" data-toggle="modal" data-target="#addCategoryModal"><i class="fa fa-plus" aria-hidden="true"></i> Keuze toevoegen</button>
	</header>
	<div class="info">
		<ul>
			<li>
			@if($name != null)
				<span class="data">{{ $name }}</span>
			@endif
			</li>
		</ul>
	</div>
	<div class="category">
		@foreach($electives as $elective)
			<div class="card_category">
				<a href="/keuzevak/{{ $elective->name }}">{{$elective->name}}</a>
			</div>
	    @endforeach
    </div>
</content>

<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Keuzevak toevoegen</h1>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ url('/addElective') }}">
                    {{ csrf_field() }}
                    <div class="input-field">
		                <input type="text" name="name">
		                <label for="name">Naam</label>
		            </div>
		            <div class="input-field">
		                <label for="start_date" class="active">Begindatum</label>
		                <input class="form-control" type="date" name="start_date">
		            </div>
		            <div class="input-field">
		                <label for="end_date" class="active">Einddatum</label>
		                <input type="date" class="" name="end_date">
		            </div>
		            <button type="submit" class="button">Opslaan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection