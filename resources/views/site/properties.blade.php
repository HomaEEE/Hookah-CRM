    <div class="columns-3">
         @if ($address == 1)
        <div class="properties">
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

        <div class="properties">
            <p><i class="icon-wifi"></i> {{ $place->properties['wifi'] }}</p>
            <p><i class="icon-key"></i> {{ $place->properties['password'] }}</p>
            <p> <i class="icon-instagram"></i> <a href="https://instagram.com/{{ $place->instagram }}"
                    target="_blank">{{ $place->instagram }}</a></p>

            <a href="wifi://:88888888@noire"><i class="arrow icon-right-open-big"></i></a>
        </div>

        @endif

        @if ($feedback == 1)

        <div class="properties comment" id="comment">
            <h4> <i class="icon-comment"></i> {{ __('Feedback button') }}</h4>
            <h4 class="hidden"> <i class="icon-comment"></i> {{ __('Feedback send') }}</h4>

            <div class="hidden-xs">
                <small>{{ __('Feedback comment') }}</small>
            </div>
        </div>

        @endif


    </div>
