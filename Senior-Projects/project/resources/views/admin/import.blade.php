{{-- Pagina om excel in te laden en te importeren in de database --}}
{{-- ToDo: Uitleg over hoe excel er moet uitzien --}}

@extends('layouts.app')

@section('content')
	<content>
		<h1>Importeer studenten</h1>
		<p><a href="/studenten.xlsx">Een voorbeeld over hoe de excel er moet uitzien, vind je hier.</a></p>
		<p>Alle huidige studenten worden verwijderd als je een nieuwe lijst importeert</p>
		@if(Session::has('imported'))
			<p>{{ Session::get('imported') }}</p>
		@endif
		<form method="POST" enctype="multipart/form-data">
			{{csrf_field()}}
			<div class="form-group">
				<label for="import_excel">Importeer excel</label>
				<input type="file" id="import_excel" name="import_excel" class="form-control">
			</div>
			<button type="submit">Importeer</button>
		</form>
		
		<h3>Huidige studenten in database</h3>
		<table id="students_all">
			<thead>
			<tr>
				<th>Voornaam</th>
				<th>Achternaam</th>
				<th>Klasgroep</th>
				<th>Klas</th>
			</tr>
			</thead>
			<tbody>
			@foreach($users as $user)
				<tr>
					<td>{{ $user->first_name }}</td>
					<td>{{ $user->surname }}</td>
					<td>{{ $user->class_group->class_group }}</td>
					<td>{{ $user->class_group->classes->abbreviation }}</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		<br>
	</content>
@endsection