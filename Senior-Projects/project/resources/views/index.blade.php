@extends('layouts.app')

@section('content')
<div class="wrapper_login">
    <div class="login">
        <form method="POST" action="{{ url('/login') }}">
            {{ csrf_field() }}
            <div class="input-field">
                <input id="emailadres" type="text" class=""  name="email" value="najoua.abdallah@student.kdg.be">
                <label for="emailadres">E-mailadres</label>
            </div>
            <div class="input-field">
                <input id="studentennummer" type="text" class="" name="student_id" value="0113578-88">
                <label for="studentennummer">Studentennummer</label>
            </div>

            @if ($errors)
                <span class="help-block">
                    <strong>{{ $errors->first() }}</strong>
                </span>
            @endif
            <button type="submit" class="button button_bevestig">Inloggen</button>
        </form>
    </div>
</div>
@endsection


