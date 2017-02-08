@extends('layouts.app')

@section('content')

<content>
	<header>
		<h2>Admin</h2>		
		<form id="logout-form" action="" method="POST">
			{{ csrf_field() }}
		 	<button type="submit" class="button"><i class="fa fa-plus" aria-hidden="true"></i> Keuzes importeren</button>
		</form>
		<button type="submit" class="button modal-trigger" data-toggle="modal" data-target="#chooseGroups"><i class="fa fa-plus" aria-hidden="true"></i> Groepen kiezen</button>
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
		@foreach($choices as $choice)
			<!--
			<div class="card_category">
				<a href="{{ url('/keuze/'.$choice->id) }}">{{ $choice->choice }}</a>
			</div>
			-->
			<div class="card_choice" id="{{ $choice->choice }}">
				<a class="card_choice_link_admin" href="/keuze/{{ $choice->id }}">{{ $choice->choice }}</a>

				@if($choice->elective->start_date < date("Y-m-d G:i:s"))
				<a class="card_choice_edit modal-trigger" data-id="{{ $choice->id }}" data-toggle="modal" data-target="#editModal" data-title="{{ $choice->choice }}" data-test="{{ $choice->test_date }}" data-start="{{ $choice->start }}" data-end="{{ $choice->end }}">
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
						<label for="name">Naam</label>
					</div>
					<div class="input-field">
						<label for="test_date" class="active">Proefdatum</label>
						<input class="form-control" type="date" name="test_date" value="">
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
            	<a href="#" class="button">Ja!</a>
                <button type="button" class="button" data-dismiss="modal">Nee</button>
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
							@foreach($classes as $class)
							{{var_dump($amounts[$loop])}}
								<div class="input-field">
									<label for="name">{{$class->class}}</label>
									<input type="number" min="0" step="1" name="number[{{$class->id}}]" value="0"/>
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