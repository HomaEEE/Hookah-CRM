<header>
    <div class="container">
    <div class="row middle-xs">
    <div class="col-xs-7 col-md-2">
        <div class="logo">
            <h1>
                <a href="/"><img src="/assets/imgs/logo.svg" alt="Noire hookah Lounge"></a>
            </h1>
        </div>
    </div>

    <div class="col-xs-5 hidden-md hidden-lg hidden-xl">
        <div class="menu-icon">
            <span class="menu-icon__line menu-icon__line-left"></span>
            <span class="menu-icon__line"></span>
            <span class="menu-icon__line menu-icon__line-right"></span>
        </div>
    </div>

    <div class="col-xs-12 col-md-10 middle-md menu-header">
        <div class="row between-xs">
        <div class="col">
            <ul class="menu">
                <li><a href="/">{{__('Home page')}}</a></li>
                <li><a href="menu">{{__('Menu page')}}</a></li>
                <li><a href="booking">{{__('Booking page')}}</a></li>
                <li><a href="feedbacks">{{__('Feedbacks')}}</a></li>
                <li><a href="/vacancy">{{__('Vacancy page')}}</a></li>
            </ul>
        </div>

        <div class="col address">
            <a href="https://www.google.com.ua/maps/place/{{$place->address}},{{$place->city}}">
                {{$place->city}},
                <span>{{$place->address}}</span>
                <a class="phone hidden-md hidden-lg hidden-xl" href="tel:0684442454">068 444 24 54</a>
            </a>
        </div>
        <div class="col langs">
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