@extends('layouts.site')
@push('css')
    <link rel="stylesheet" href="/assets/css/style.css">
@endpush

@section('page', __('Booking page'))

@section('content')
<div class="page-container">

    @include('site.nav')

    <div class="container">

        <h2 class="divider line razor title">{{__('Booking')}}</h2>
        <div class="row middle-xs beetwen-xs">
        <div class="col-xs-12 col-md-5">
        <div class="reserve-message hidden">
            <h3>{{__('reserve-message')}}</h3>
            <h4>{{__('reserve-message-content')}}</h4>
        </div>
        <form data-action="/sendReserve" method="POST" id="reserve" class="form">
            @csrf
            <input type="hidden" name="place_id" value="{{ $place->id }}" id="place_id">

            <div class="input-group">
                <input type="text" name="name" id="name" placeholder=" " required/>
                <label for="name">{{__('Name')}}</label>
              </div>

              <div class="input-group">
                <input type="tel" name="phone" id="phone" placeholder=" " required/>
                <label for="phone">{{__('Phone')}}</label>
              </div>

              <div class="input-group">
                <input type="number" name="person" id="person" placeholder=" " required/>
                <label for="person">{{__('Persons')}}</label>
              </div>

              <div class="input-group">
                <input  name="date" id="date" placeholder="{{__('Date placeholder')}}" required/>
                <label for="date">{{__('Date')}}</label>
              </div>

              <div class="input-group">
                <input name="time" id="time" placeholder="12:00" required/>
                <label for="time">{{__('Time')}}</label>
              </div>

            <button type="submit" class="button-success">{{__('Send')}}</button>

        </form>
        </div>
        <div class="col-xs-12 col-md-2">
            <h3 class="divider line razor rotate">{{__('OR')}}</h3>
        </div>

        <div class="col-xs-12 col-md-5">
        <h2>{{$place->phone}}</h2>

        <h3> <a href="https://www.google.com.ua/maps/place/{{$place->address}},{{$place->city}}" target="_blank"><span>{{$place->address}}</span></a> </h3>
        <h3>{{$place->time['day']}}</h3>
        </div>
    </div>
    </div>

@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paroller.js/1.4.7/jquery.paroller.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js"></script>

<script src="/assets/js/odessa.js"></script>
@endpush
