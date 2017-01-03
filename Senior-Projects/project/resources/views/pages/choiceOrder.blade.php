@extends('layouts.app')

@section('content')

<content>
    <header>
        <h2>Keuze</h2>        
        <form id="logout-form" action="/logout" method="POST">
            {{ csrf_field() }}
            <button type="submit" class="button">Uitloggen</button>
        </form>
    </header>
    <div class="info">
        <ul>
            <li>
                <span class="label">Naam:</span>
                <span class="data">Naam student</span>
            </li>
            <li>
                <span class="label">E-mailadres:</span>
                <span class="data">E-mailadres student</span>
            </li>
            <li>
                <span class="label">Studentennummer:</span>
                <span class="data">Studentennummer student</span>
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
	                    <input type="hidden" name="choice[]" value="{{ $choice->id }}">
	                </div>
				@endforeach
			</div>
			<div class="container_button">
                <button type="submit" class="button">Bevestig</button>
            </div>
        </form>
    </div>
</content>

@endsection