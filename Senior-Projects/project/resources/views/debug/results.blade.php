{{--Lander, DO NOT STYLE - This is a debug page--}}
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	
	<title>{{ config('app.name', 'Keuzetool OM') }}</title>
	
	<!-- Styles -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
		  integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	
	<style>
		.results tr[visible='false'],
		.no-result
		{
			display: none;
		}
		
		.results tr[visible='true']
		{
			display: table-row;
		}
		
		.counter
		{
			padding: 8px;
			color: #ccc;
		}
	</style>
	
	<!-- Scripts -->
	<script>
		window.Laravel = <?php echo json_encode( [
			'csrfToken' => csrf_token(),
		] ); ?>
	</script>
</head>
<body>
<div class="col-md-12">
	<div class="form-group pull-right">
		<input type="text" class="search form-control" placeholder="What you looking for?">
	</div>
	<span class="counter pull-right"></span>
	
	<table class="table table-hover table-bordered table-striped results">
		<thead>
		<tr>
			<th>#</th>
			<th>User</th>
			@for($i = 1; $i <= $pickCounter; $i++)
				<th>Pick {{ $i }}</th>
			@endfor
		</tr>
		<tr class="warning no-result">
			<td colspan="{{$pickCounter+1}}"><i class="fa fa-warning"></i> No result</td>
		</tr>
		</thead>
		<tbody>
		@foreach($results as $userId => $big)
			<tr>
				<td>{{ $loop->index }}</td>
				<td>{{ $userId }}</td>
				@foreach($big as/*s*/ $dick)
					<td>{{$dick->choices->choice}} ({{$dick->choices->id}})</td>
				@endforeach
			</tr>
		@endforeach
		</tbody>
	</table>
</div>
<!-- Scripts -->
<script
		src="https://code.jquery.com/jquery-3.1.1.min.js"
		integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
		crossorigin="anonymous">
</script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
		integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
		crossorigin="anonymous"></script>

<script>
	$(document).ready(function () {
		$(".search").keyup(function () {
			var searchTerm = $(".search").val();
			var listItem = $('.results tbody').children('tr');
			var searchSplit = searchTerm.replace(/ /g, "'):containsi('")
			
			$.extend($.expr[ ':' ], {
				'containsi': function ( elem, i, match, array ) {
					return (elem.textContent || elem.innerText || '').toLowerCase().indexOf((match[ 3 ] || "").toLowerCase()) >= 0;
				}
			});
			
			$(".results tbody tr").not(":containsi('" + searchSplit + "')").each(function ( e ) {
				$(this).attr('visible', 'false');
			});
			
			$(".results tbody tr:containsi('" + searchSplit + "')").each(function ( e ) {
				$(this).attr('visible', 'true');
			});
			
			var jobCount = $('.results tbody tr[visible="true"]').length;
			$('.counter').text(jobCount + ' item');
			
			if (jobCount == '0') {
				$('.no-result').show();
			}
			else {
				$('.no-result').hide();
			}
		});
	});
</script>
</body>
</html>