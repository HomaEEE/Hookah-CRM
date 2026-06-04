{{-- resources/views/errors/404.blade.php --}}

@extends('layouts.site')

@section('page', __('Page not found'))
@section('title', __('Page not found'))
@section('description', __('404 error '))

@section('content')
  <div class="error-page">
            <h1 class="title">{{ __('Page not found') }}</h1>

            <div class="error-code">
                <span class="digit">4</span>
                <div class="bowl-container">
                    <img src="{{ asset('assets/imgs/404/bowl.png') }}" alt="0" class="static-bowl" id="staticBowl">
                    <div class="slideshow-mask-wrapper" id="slideshowWrapper">
                        <img src="{{ asset('assets/imgs/404/slides/1.jpg') }}" class="slide active" alt="Slide 1">
                        <img src="{{ asset('assets/imgs/404/slides/2.jpg') }}" class="slide" alt="Slide 2">
                        <img src="{{ asset('assets/imgs/404/slides/3.jpg') }}" class="slide" alt="Slide 3">
                        <img src="{{ asset('assets/imgs/404/slides/4.jpg') }}" class="slide" alt="Slide 4">
                        <img src="{{ asset('assets/imgs/404/slides/5.jpg') }}" class="slide" alt="Slide 4">
                    </div>
                </div>
                <span class="digit">4</span>
            </div>

            <div class="buttons-container">
                <a href="{{ route('home') }}" class="btn btn-outline">
                    {{ __('Back to home') }} <span class="arrow">→</span>
                </a>
                 <a href="{{ route('menu') }}" class="btn btn-outline">
                    {{ __('Menu') }} <span class="arrow">→</span>
                </a>
            </div>
            </div>

@endsection

