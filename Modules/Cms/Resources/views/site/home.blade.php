@extends('cms::layouts.site')

@section('page', __('Home page'))
@section('title', __('Home page'))
@section('description', __('Home page description'))

@section('content')

    @include('cms::site.partials.home.hero')
    @include('cms::site.partials.home.about')
    @include('cms::site.partials.home.reserve')
    @include('cms::site.partials.home.menu')
    @include('cms::site.partials.home.feedbacks')
    @include('cms::site.partials.home.footer')

@endsection