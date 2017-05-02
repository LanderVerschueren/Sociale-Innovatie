@extends('layouts.app')

@section('content')

<content>
	<header>
		<h2>Admin</h2>
		<button type="submit" class="button modal-trigger" data-toggle="modal" data-target="#addChoice"><i class="fa fa-plus" aria-hidden="true"></i> Keuze toevoegen</button>
		<button type="submit" class="button modal-trigger" data-toggle="modal" data-target="#chooseGroups"><i class="fa fa-plus" aria-hidden="true"></i> Groepen kiezen</button>
				
		<form id="logout-form" action="/logout" method="POST">
			{{ csrf_field() }}
		 	<button type="submit" class="button">Uitloggen</button>
		</form>
	</header>
	<div class="info">
		<ul>
			<li>
			@if($elective->name != null)
				<span class="data">{{ $elective->name }}</span>
			@endif
			</li>
		</ul>
	</div>
	<div class="choice">
		<div class="card_container">

		@if($choices != null)
			@foreach($choices as $choice)
				<!--
				<div class="card_category">
					<a href="{{ url('/keuze/'.$choice->id) }}">{{ $choice->choice }}</a>
				</div>
				-->
				<div class="card_choice" id="{{ $choice->choice }}">
					<a class="card_choice_link_admin" href="/keuze/{{ $choice->id }}">{{ $choice->choice }}</a>

					@if($choice->elective->start_date < date("Y-m-d G:i:s"))
					<a class="card_choice_edit modal-trigger" data-id="{{ $choice->id }}" data-toggle="modal" data-target="#editChoice" data-title="{{ $choice->choice }}" data-description="{{ $choice->description }}" data-min="{{ $choice->minimum }}" data-max="{{ $choice->maximum }}">
						<i class="fa fa-cog" aria-hidden="true"></i>
					</a>
					<a class="card_choice_delete modal-trigger" data-id="{{ $choice->id }}" data-toggle="modal" data-target="#deleteModal" data-title="{{ $choice->choice }}">
						<i class="fa fa-trash" aria-hidden="true"></i>
					</a>
					@endif
				</div>
			@endforeach
		@endif
		</div>
    </div>
</content>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title" id="editModalLabel"></h1>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ url('/addElective') }}">
					{{ csrf_field() }}
					<div class="input-field">
						<input type="text" name="name">
						<label for="name" >Naam</label>
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
            	<a href="" id="deleteChoice" class="button">Ja!</a>
                <button type="button" class="button" data-dismiss="modal">Nee</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addChoice" tabindex="-1" role="dialog" aria-labelledby="addChoiceLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title" >Voeg een keuze toe</h1>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ url('/addChoice/'.$elective->name) }}">
					{{ csrf_field() }}

					<div class="input-field">
						<label for="name">Naam keuze: </label>
						<input type="text"  name="choice"/>
					</div>
					<div class="input-field">
						<label for="name">Beschrijving: </label>
						<textarea name="description" placeholder="Typ hier de beschrijving van het vak."></textarea>
					</div>
					<div class="input-field">
						<label for="name">Minimum aantal studenten: </label>
						<input type="number" min="0" step="1" name="minimum" />
					</div>
					<div class="input-field">
						<label for="name">Maximaal aantal studenten:</label>
						<input type="number" min="0" step="1" name="maximum"/>
					</div>
					@foreach($classgroups as $indexKey => $group)
						<div class="input-field classes">
							<label for="name">{{$group->class_group}}</label>
							<input type="checkbox" name="group[{{$group->id}}]" value="{{$group->id}}"/>
						</div>
					@endforeach
					<button type="submit" class="button">Opslaan</button>
				</form>
			</div>
			<div class="modal-footer">

			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="editChoice" tabindex="-1" role="dialog" aria-labelledby="editChoiceLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title" >Voeg een keuze toe</h1>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ url('/updateChoice/'.$elective->name) }}">
					{{ csrf_field() }}

					<div class="input-field">
						<label for="choiceName">Naam keuze: </label>
						<input type="text" id="choiceName" name="choice" placeholder="Naam"/>
					</div>
					<div class="input-field">
						<label for="choiceDescription" class="active">Beschrijving: </label>
						<textarea id="choiceDescription" name="description" style="margin-bottom: 15px;" placeholder="Typ hier de beschrijving van het vak."></textarea>
					</div>
					<div class="input-field">
						<label for="choiceMin">Minimum aantal studenten: </label>
						<input type="number" id="choiceMin" min="0" step="1" name="minimum" placeholder="minimum" />
					</div>
					<div class="input-field">
						<label for="choiceMax">Maximaal aantal studenten:</label>
						<input type="number" id="choiceMax" min="0" step="1" name="maximum" placeholder="maximum"/>
					</div>
					@foreach($classgroups as $indexKey => $group)
						<div class="input-field classes">
							<label for="name">{{$group->class_group}}</label>
							<input type="checkbox" name="group[{{$group->id}}]" value="{{$group->id}}"/>
						</div>
					@endforeach
					<button type="submit" class="button">Opslaan</button>
				</form>
			</div>
			<div class="modal-footer">

			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="chooseGroups" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title">Maximum aantal mensen per richting</h1>
			</div>
			<div class="modal-body">
				@if($classes != null)
					<form method="POST" action="{{ url('/giveAmountToElective/'.$elective->id) }}">
							{{ csrf_field() }}
							@foreach($classes as $indexKey => $class)
								<div class="input-field">
									<label for="name">{{$class->class}}</label>
									<input type="number" min="0" step="1" name="number[{{$class->id}}]" value="{{$amounts[$indexKey]->amount}}"/>
								</div>
							@endforeach
						<button type="submit" class="button">Opslaan</button>
					</form>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection