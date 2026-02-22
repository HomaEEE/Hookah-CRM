@extends('layouts.site')
@push('css')
    <link rel="stylesheet" href="/assets/css/style.css">
@endpush

@section('page', __('Feedbacks'))

@section('content')
    <div class="container">
        <h2>{{__('Feedbacks')}}</h2>
        @include('site.partials.advice', ['address' => 1, 'wifi' => 0, 'feedback'=> 1])

        <div class="grid grid-cols-3 xs:grid-cols-1 gap-4">
        @foreach ($feedbacks as $f)
            <div class="feedback">
                <h4>{{ $f->name}}</h4>
                <p>{{ $f->text}}</p>
            </div>
        @endforeach
        </div>
    </div>

@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paroller.js/1.4.7/jquery.paroller.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js"></script>

<script src="/assets/js/odessa.js"></script>
@endpush
