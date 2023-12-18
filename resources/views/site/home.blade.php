@extends('layouts.site')
@push('css')
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet">
@endpush

@section('page', __('Home page'))

@section('content')
<div class="page-container">
    <div class="hero">
        <div class="container">
        <section class="splide" aria-label="Basic Structure Example">
        <div class="splide__track">
            <ul class="splide__list">
            <li class="splide__slide">
                <span class="text">
                    <h2>{{ __('Hookahs') }}</h2>
                </span>
                <img src="{{ asset('assets/imgs/hero/hookah.png') }}" alt="">
            </li>
            </ul>
        </div>

        <section class="links">
            <li><a href="/menu">{{ __('Menu') }}</a></li>
            <li><a href="/booking">{{ __('Booking') }}</a></li>
            <li><a href="/feedbacks">{{ __('Feedbacks') }}</a></li>
        </section>

        </section>


      </div>
    </div>

    <div class="about">
        <div class="container">
            test
            test
            test
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paroller.js/1.4.7/jquery.paroller.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>

<script src="/assets/js/home.js"></script>
<script src="/assets/js/odessa.js"></script>
@endpush
