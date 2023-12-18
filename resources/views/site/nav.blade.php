<header>
    <div class="container">
    <button class="burger burger2"><span></span></button>
    <div class="row  middle-md around-md">
    <div class="col-xs-6 col-md-2">
        <div class="logo">
        <h1>
            <a href="/"><img src="/assets/imgs/logo.svg" alt="Noire hookah Lounge"></a>
        </h1>
    </div>
    </div>
    <div class="col-xs-6 hidden-xs col-md-10 middle-md">
        <div class="row">
        <div class="col-xs">
            <ul class="menu">
                <li><a href="/">{{__('Home page')}}</a></li>
                <li><a href="menu">{{__('Menu page')}}</a></li>
                <li><a href="booking">{{__('Booking page')}}</a></li>
                <li><a href="feedbacks">{{__('Feedbacks')}}</a></li>
                <li><a href="/vacancy">{{__('Vacancy page')}}</a></li>
            </ul>
        </div>

        <div class="col-xs-12 col-md-2 address">
            <a href="https://www.google.com.ua/maps/place/{{$place->address}},{{$place->city}}">
                {{$place->city}},
                <span>{{$place->address}}</span>
            </a>
        </div>
        <div class="col-xs-12 col-md-2 langs">
                @foreach (Config::get('app.languages') as $lang => $language)
                    @if ($lang != App::getLocale())
                    <li>
                        <a href="{{ route('lang.switch', $lang) }}"> {{$language}}</a>
                     </li>
                    @else
                    <li class="active">
                         <a href="{{ route('lang.switch', $lang) }}"> {{$language}}</a>
                    </li>
                    @endif
                </li>
                @endforeach
    </div>
    </div>

</div>
</div>
</header>