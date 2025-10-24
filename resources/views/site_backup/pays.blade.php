@extends('layouts.site')
@push('css')
    <link rel="stylesheet" href="/assets/css/style.css">
@endpush

@section('page', __('Pay page'))

@section('content')
<div class="page-container">

    @include('site.nav')

    <div class="container">
        <div class="row middle-xs">

            <div class="col-xs-12 col-md-6 pay">
                <a href="{{ $place->pays['mono'] }}">
                    <img src="/assets/imgs/logos/mono.png">
                    <span>{{$place->pays['mono_card']}}</span>
                </a>
             </div>

             <div class="col-xs-12 col-md-6 pay">
                <a href="{{ $place->pays['pb'] }}">
                    <img src="/assets/imgs/logos/pb.png">
                    <span>{{$place->pays['pb_card']}}</span>
                </a>
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
