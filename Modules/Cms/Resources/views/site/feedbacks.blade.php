@extends('cms::layouts.site')
@push('css')
    <link rel="stylesheet" href="/assets/css/style.css">
@endpush

@section('page', __('Feedbacks'))

@section('content')
    <div class="container">
        <h2 class="divider line razor title">{{__('Feedbacks')}}</h2>

        @include('cms::site.partials.advice', ['address' => 1, 'wifi' => 0, 'feedback'=> 1])

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 md:gap-4">
        @foreach ($feedbacks as $f)
            <div class="feedback">
                <p>{{ $f->text}}</p>
                <span class="name">{{ $f->name}}</span>
                <span class="date">{{ $f->created_at->format('d.m.Y')}}</span>

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
