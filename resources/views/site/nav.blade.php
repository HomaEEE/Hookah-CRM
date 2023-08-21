<header>
    <div class="container">
    <div class="logo">
        <h1>
            <a href="/"><img src="/assets/imgs/logo.svg" alt="Noire hookah Lounge"></a>
        </h1>
    </div>
    <button class="burger burger2"><span></span></button>
    <div class="row between-xs header">
    <nav class="col-xs-12 col-md-5">
        <ul class="menu">
            <li><a href="/">{{__('Home page')}}</a></li>
            <li><a href="menu">{{__('Menu page')}}</a></li>
            <li><a href="booking">{{__('Booking page')}}</a></li>
            <li><a href="feedbacks">{{__('Feedbacks')}}</a></li>
            <!--li><a href="/{{ Request::segment(1) }}/vacancy">{{__('Vacancy page')}}</li-->
        </ul>
    </nav>
            <div class="col-xs-12 col-md-4 middle-xs">
                <div class="row">

            <div class="col-xs-12 col-md-6 address">
                <div class="row">
                    <a href="https://www.google.com.ua/maps/place/{{$place->address}},{{$place->city}}">
                        {{$place->city}},
                        <span>{{$place->address}}</span>
                    </a>
                </div>
                <div class="row">
                    <a class="tel" href="tel:{{$place->phone}}">{{$place->phone}}</a>
            </div>
        </div>

            <ul class="col-xs-12 col-md-6 langs">
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
            </ul>

        </div>
    </div>
</div>
</div>
</header>