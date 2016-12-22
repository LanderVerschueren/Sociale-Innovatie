{{-- Pagina om excel in te laden en te importeren in de database --}}
{{-- ToDo: Uitleg over hoe excel er moet uitzien --}}

@extends('layouts.app')

@section('content')
	<content>
		<h1>Importeer studenten</h1>
		<form method="POST" enctype="multipart/form-data">
			{{csrf_field()}}
			<div class="form-group">
				<label for="import_excel">Importeer excel</label>
				<input type="file" id="import_excel" name="import_excel" class="form-control">
			</div>
			<button type="submit">Importeer</button>
		</form>
	</content>
@endsection