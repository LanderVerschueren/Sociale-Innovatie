@extends('layouts.app')

@section('content')

<content>
    @if($message)
        <p>{{$message}}</p>
    @endif
    <h2>Keuze</h2>
    <div class="info">
        <ul>
            <li>
                <span class="label">Naam:</span>
                <span class="info">{{Auth::user()->first_name}} {{Auth::user()->surname}}</span>
            </li>
            <li>
                <span class="label">E-mailadres:</span>
                <span class="info">{{Auth::user()->email}}</span>
            </li>
            <li>
                <span class="label">Studentennummer:</span>
                <span class="info">{{Auth::user()->student_id}}</span>
            </li>
        </ul>
    </div>
    <div class="choice">
        <form action="/rightOrder" method="post">
            {{ csrf_field() }}
            <div>
                @foreach($choices as $choice)                
                    <div class="card_choice" id="{{ $choice->choice }}">
                        <a class="card_choice_link" href="{{ $choice->choice }}">{{$choice->choice}}</a>
                        <a class="card_choice_info modal-trigger" data-id="{{ $choice->id }}" data-toggle="modal" data-target="#favoritesModal">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                        </a>
                        <input type="checkbox" name="{{$choice->choice}}" value="{{$choice->id}}">
                    </div>
                @endforeach
            </div>
            <div class="container_button">
                <button type="submit">Bevestig</button>
            </div>
        </form>
    </div>
</content>

<div class="modal fade" id="favoritesModal" 
     tabindex="-1" role="dialog" 
     aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" 
          data-dismiss="modal" 
          aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" 
        id="favoritesModalLabel">The Sun Also Rises</h4>
      </div>
      <div class="modal-body">
        <p>
        Please confirm you would like to add 
        <b><span id="fav-title">The Sun Also Rises</span></b> 
        to your favorites list.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" 
           class="btn btn-default" 
           data-dismiss="modal">Close</button>
        <span class="pull-right">
          <button type="button" class="btn btn-primary">
            Add to Favorites
          </button>
        </span>
      </div>
    </div>
  </div>
</div>
@endsection