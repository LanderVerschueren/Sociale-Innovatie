@extends('layouts.app')

@section('content')
<div class="wrapper_login">
    <div class="login">
        <form action="">
            <div class="input-field">
                <input id="emailadres" type="text" class="validate">
                <label for="emailadres">E-mailadres</label>
            </div>
            <div class="input-field">
                <input id="studentennummer" type="text" class="validate">
                <label for="studentennummer">Studentennummer</label>
            </div>
        </form>
    </div>
</div>
@endsection
