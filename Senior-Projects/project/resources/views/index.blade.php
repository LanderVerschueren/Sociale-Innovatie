@extends('layouts.app')

@section('content')
<div class="wrapper_login">
    <div class="login">
        <form action="/category" method="GET">
            <div class="input-field">
                <input id="emailadres" type="text" class="">
                <label for="emailadres">E-mailadres</label>
            </div>
            <div class="input-field">
                <input id="studentennummer" type="text" class="">
                <label for="studentennummer">Studentennummer</label>
            </div>
            <button type="submit" class="button_bevestig">Inloggen</button>
        </form>
    </div>
</div>
@endsection
