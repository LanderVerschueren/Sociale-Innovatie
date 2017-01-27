@extends('layouts.app')

@section('content')

<content>
	<header>
		<h2>Admin</h2>		
		<form id="logout-form" action="" method="POST">
			{{ csrf_field() }}
		 	<button type="submit" class="button"><i class="fa fa-plus" aria-hidden="true"></i> Keuzes importeren</button>
		</form>
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
	<div class="choice">
		<div class="card_container">
		@foreach($choices as $choice)
			<!--
			<div class="card_category">
				<a href="{{ url('/keuze/'.$choice->id) }}">{{ $choice->choice }}</a>
			</div>
			-->
			<div class="card_choice" id="{{ $choice->choice }}">
				<a class="card_choice_link_admin" href="/keuze/{{ $choice->id }}">{{ $choice->choice }}</a>

				@if($choice->elective->start_date < date("Y-m-d G:i:s"))
				<a class="card_choice_edit modal-trigger" data-id="{{ $choice->id }}" data-toggle="modal" data-target="#editModal" data-title="{{ $choice->choice }}">
					<i class="fa fa-cog" aria-hidden="true"></i>
				</a>
				<a class="card_choice_delete modal-trigger" data-id="{{ $choice->id }}" data-toggle="modal" data-target="#deleteModal" data-title="{{ $choice->choice }}">
					<i class="fa fa-trash" aria-hidden="true"></i>
				</a>
				@endif
			</div>
	    @endforeach
		</div>
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
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="editModalLabel"></h1>
            </div>
            <div class="modal-body">
                <p id="editModalParagraph"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="button" data-dismiss="modal">Sluiten</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="deleteModalLabel"></h1>
            </div>
            <div class="modal-body">
                Wilt u dit vak verwijderen?
            </div>
            <div class="modal-footer">
            	<a href="#" class="button">Ja!</a>
                <button type="button" class="button" data-dismiss="modal">Nee</button>
            </div>
        </div>
    </div>
</div>
@endsection