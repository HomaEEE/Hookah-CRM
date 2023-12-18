@extends('layouts.site')
@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="/assets/css/style.css">
@endpush

@section('page', __('Home page'))

@section('content')
<div class="page-container">
        <div class="hero">
        <div class="container">
            <div class="swiper">
                <div class="swiper-wrapper">
                    <!-- Slides -->
                    <div class="swiper-slide">
                    <span class="text">
                                <h2>{{ __('Hookahs') }}</h2>
                            </span>
                            <img src="{{ asset('assets/imgs/hero/hookah.png') }}" alt="">
                    </div>
                    <div class="swiper-slide">
                    <span class="text">
                                <h2>{{ __('Hookahs') }}</h2>
                            </span>
                            <img src="{{ asset('assets/imgs/hero/hookah.png') }}" alt="">
                    </div>
                    <div class="swiper-slide">
                    <span class="text">
                                <h2>{{ __('Hookahs') }}</h2>
                            </span>
                            <img src="{{ asset('assets/imgs/hero/hookah.png') }}" alt="">
                    </div>
                </div>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>

        <section class="links">
            <li><a href="/menu">{{ __('Menu') }}</a></li>
            <li><a href="/booking">{{ __('Booking') }}</a></li>
            <li><a href="/feedbacks">{{ __('Feedbacks') }}</a></li>
        </section>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.5/swiper-bundle.min.js"></script>

<script src="/assets/js/home.js"></script>
<script src="/assets/js/odessa.js"></script>
@endpush
