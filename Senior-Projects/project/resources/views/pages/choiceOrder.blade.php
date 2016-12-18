@extends('layouts.app')

@section('content')

<content>
    <h2>Keuze</h2>
    <div class="info">
        <ul>
            <li>
                <span class="label">Naam:</span>
                <span class="info">Naam student</span>
            </li>
            <li>
                <span class="label">E-mailadres:</span>
                <span class="info">E-mailadres student</span>
            </li>
            <li>
                <span class="label">Studentennummer:</span>
                <span class="info">Studentennummer student</span>
            </li>
        </ul>
    </div>
    <div class="choice">
        <form action="/storeOrder" method="post">
            {{ csrf_field() }}
            <div id="sortable">
	            @foreach($choices as $choice)
					<div class="card_choice_order" id="{{ $choice->choice }}">
	                    <a class="card_choice_order_link" href="">{{$choice->choice}}</a>
	                    
	                </div>
				@endforeach
			</div>
			<div class="container_button">
                <button type="submit">Bevestig</button>
            </div>
        </form>
    </div>
</content>

				<!--
				<p>{{$choice->choice}}</p>
				<input type="number" name="{{$choice->id}}">
				<input type="number" name="{{$choice->id}}" id="{{ $choice->choice }}">
				-->
@endsection