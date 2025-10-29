<div class="advice p-6 my-4 mx-auto grid grid-cols-2 md:grid-cols-3 gap-12 md:divide-x-1 divide-[#535353]">
         @if ($address == 1)
        <div class=" hidden md:block">
                {{-- <p> <i class="icon-map-pin"></i> <a
                        href="https://www.google.com.ua/maps/place/{{ $place->address }},{{ $place->city }}"
                        target="_blank"><span>{{ $place->address }}</span></a> </p> --}}
                <p> <i class="icon-mobile"></i> <a class="tel" href="tel:{{ $place->phone }}">{{ $place->phone }}</a></p>
                <p> <i class="icon-clock"></i> {{ $place->time['day'] }}</a></p>
                <p> <i class="icon-instagram"></i> <a href="https://instagram.com/{{ $place->instagram }}"
                        target="_blank">{{ $place->instagram }}</a></p>

        </div>
        @endif

        @if ($wifi == 1)

        <div class="">
            <p><i class="icon-wifi"></i> {{ $place->properties['wifi'] }}</p>
            <p><i class="icon-key"></i> {{ $place->properties['password'] }}</p>
            <p> <i class="icon-instagram"></i> <a href="https://instagram.com/{{ $place->instagram }}"
                    target="_blank">{{ $place->instagram }}</a></p>
        </div>

        @endif

        @if ($feedback == 1)

        <div class=" comment" id="comment">
            <h4> <i class="icon-comment"></i> {{ __('Feedback button') }}</h4>
                <p>{{ __('Feedback comment') }}</p>

        </div>

        @endif

</div>