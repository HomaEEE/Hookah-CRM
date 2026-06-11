@extends('cms::layouts.site')
@push('css')
    <link rel="stylesheet" href="/assets/css/style.css">
@endpush

@section('page', __('Table page'))

@section('content')
<div class="container page">
    <div class="container">

        <h2 class="divider line razor title">{{__('Table')}} {{ $table_number }}</h2>
        <div class="row middle-xs beetwen-xs">
        <div class="col-xs-12">
          <a class="btn" href="/menu">{{__('View')}} {{__('Menu')}}</a>
          <a  class="btn" href="/feedback">{{__('Feedback button')}}</a>

          <a class="btn" href="/bot/waiter">{{__('Call')}} {{__('Waiter')}}</a>
          <a class="btn" href="/bot/hookah">{{__('Call')}} {{__('Hookah Master')}}</a>
          <a  class="btn" href="/bot/bill">{{__('Bill')}}</a>
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
