@extends('layouts.index')
@push('css')
    <link rel="stylesheet" href="/assets/css/style.css">
@endpush
@section('page', __('Index page'))

@section('content')
<div class="index-container">
    <div class="logo-index">
        <h1 class="logo">
            <img src="/assets/imgs/logo.svg" alt="Noire hookah Bar">
        </h1>

        <ul class="langs_index">
            @foreach (Config::get('app.languages') as $lang => $language)
                        @if ($lang != App::getLocale())
                        <li>
                                <a href="{{ route('lang.switch', $lang) }}"> {{$language}}</a>
                         </li>

                        @else
                        <li class="active">
                             <a href="{{ route('lang.switch', $lang) }}"> {{$language}}</a>
                        </li>
                        @endif
                    </li>
                    @endforeach
                </ul>
    </div>
    <div class="row between-xs">
        @foreach ($places as $place)
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-6 place-container">
            <div class="text">
                <h2>{{$place->city}}</h2>
                <h3><span class="icon-map-pin"></span> <a href="https://maps.google.com/?q={{__($place->address)}}, {{$place->city}}"> {{$place->address}}</a></h3>
                <h3><span class="icon-mobile"></span> <a href="tel:{{$place->phone}}" target="_blank">{{$place->phone}}</a></h3>
                <h3><span class="icon-instagram"></span> <a href="https://instagram.com/{{$place->instagram}}"  target="_blank">{{$place->instagram}}</a></h3>
                @if ($place->opened == 1)
                <a href="/{{$place->slug}}/menu" class="btn">{{__('Go to Menu')}}</a>
                @else
                    <span class="closed">{{__('Closed')}}</span>
                @endif
            </div>
            <img src="/assets/imgs/places/{{$place->slug}}/home.jpg" alt="">
        </div>
        @endforeach
    </div>
</div>
@endsection