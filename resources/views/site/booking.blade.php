@extends('layouts.site')
@push('css')
  <link rel="stylesheet" href="/assets/css/style.css">
@endpush

@section('page', __('Booking page'))

@section('content')
  <div class="container">

    <h2 class="divider line razor title">{{__('Booking')}}</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 items-center ">
      <div class="booking-form p-0 sm:pr-12">
        <form data-action="/sendReserve" method="POST" id="reserve" class="form">
          @csrf
          <input type="hidden" name="place_id" value="{{ $place->id }}" id="place_id">

          <div class="input-group">
            <input type="text" name="name" id="name" required />
            <label for="name">{{__('Name')}}</label>
          </div>

          <div class="input-group">
            <input type="tel" name="phone" id="phone" required />
            <label for="phone">{{__('Phone')}}</label>
          </div>

          <div class="input-group">
            <input type="number" name="person" id="person" required min="2" />
            <label for="person">{{__('Persons')}}</label>
          </div>

          <div class="input-group">
            <input name="date" id="date" type="date" placeholder="{{__('Date placeholder')}}" required />
            <label for="date">{{__('Date')}}</label>
          </div>

          <div class="input-group">
            <input name="time" id="time" type="time" required />
            <label for="time">{{__('Time')}}</label>
          </div>

          <button type="submit" class="button-success">{{__('Send')}}</button>

        </form>
      </div>

      <div class="booking-contact border-l border-[#535353] px-8">
        <div class="grid grid-cols-2 w-full gap-4">
                    <div>{{ __('Address') }}</div>
                    <div class="pb-8">
                        <span class="block">{{ $place->city }}, UA</span>
                        <span class="block">{{ $place->address ?? 'Nina Pestroy. 27' }}</span>
                        <span class="block">{{ $place->time['day'] ?? '12:00 - 22:30' }}</span>
                    </div>
                </div>

                    <div class="grid grid-cols-2 w-full gap-4">
                    <div>{{ __('Contacts') }}</div>
                    <div class="pb-8">
                        <span class="block"><a href="tel:{{ $place->phone }}" >{{ $place->phone }}</a></span>
                        <span class="block"><a href="{{ $place->instagram }}">instagram</a></span>
                        <span class="block"><a href="{{ $place->telegram }}">telegram</a></span>
                    </div>
                </div>
                <div class="grid grid-cols-1 w-full gap-4">
                                      <div>{{ __('Map') }}</div>
                  <div>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d4952.225703712118!2d30.72989315105745!3d46.480031735277045!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40c631621c0e4c09%3A0x6d430f5beb2ac384!2sNoire%20Smoke%20Different!5e0!3m2!1sru!2sua!4v1773188948310!5m2!1sru!2sua" style="border:0;" allowfullscreen="" width="100%" height="250px" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                  </div>
        </div>

@endsection

      @push('scripts')

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"
          integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA=="
          crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/paroller.js/1.4.7/jquery.paroller.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js"></script>

        <script src="/assets/js/odessa.js"></script>
      @endpush