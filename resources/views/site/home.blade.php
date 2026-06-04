@extends('layouts.site')

@section('page', __('Home page'))
@section('title', __('Home page'))
@section('description', __('Home page description'))

@section('content')

    @include('site.partials.home.hero')
    @include('site.partials.home.about')
    @include('site.partials.home.reserve')
    @include('site.partials.home.menu')
    @include('site.partials.home.feedbacks')
    @include('site.partials.home.footer')

@endsection