@extends('layouts.site')
@push('css')
    <link rel="stylesheet" href="/assets/css/style.css">
@endpush

@section('page', __('Booking page'))

@section('content')
    <div class="container">

        <h2 class="divider line razor title">{{__('Booking')}}</h2>
        <div class="flex flex-column sm:flex-row items-center">
        <div  class="basis-256">

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
                <input type="number" name="person" id="person" placeholder=" " required min="2"/>
                <label for="person">{{__('Persons')}}</label>
              </div>

              <div class="input-group">
                <input  name="date" id="date" type="date" placeholder="{{__('Date placeholder')}}" required/>
                <label for="date">{{__('Date')}}</label>
              </div>

              <div class="input-group">
                <input name="time" id="time"  type="time" required/>
                <label for="time">{{__('Time')}}</label>
              </div>

            <button type="submit" class="button-success">{{__('Send')}}</button>

        </form>
        </div>
        <div class="basis-32">
            <h3 class="divider line razor rotate">{{__('OR')}}</h3>
        </div>

        <div  class="basis-256">
        <span>{{$place->city}}</span>
        <span><a href="https://www.google.com.ua/maps/place/{{$place->address}},{{$place->city}}" target="_blank"><span>{{$place->address}}</span></a> </span>
        <span>{{$place->time['day']}}</span>
        <span><a href="tel:{{$place->phone}}">{{$place->phone}}</a><span>

        </div>
    </div>

@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paroller.js/1.4.7/jquery.paroller.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js"></script>

<script src="/assets/js/odessa.js"></script>
@endpush
