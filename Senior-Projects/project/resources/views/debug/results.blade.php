{{--Lander, DO NOT STYLE--}}

@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<table class="table table-striped">
						<thead>
						<tr>
							<th>User</th>
							@for($i = 1; $i <= $pickCounter; $i++)
								<th>Pick {{ $i }}</th>
							@endfor
						</tr>
						</thead>
						<tbody>
						@foreach($results as $userId => $big)
							<tr>
								<td>{{ $userId }}</td>
								@foreach($big as/*s*/ $dick)
									<td>{{$dick->choices->choice}} ({{$dick->choices->id}})</td>
								@endforeach
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection