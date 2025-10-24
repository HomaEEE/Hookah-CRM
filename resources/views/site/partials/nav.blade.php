<header class="container p-4">
    <div class="flex flex-row justify-between items-center">
        <div class="flex-auto">
            <h1>
                <a href="/"><img class="w-32" src="/assets/imgs/logo.svg" alt="Noire hookah Lounge"></a>
            </h1>
        </div>

  <div class="flex-auto">
    <div class="menu-icon md:hidden">
            <span class="menu-icon__line menu-icon__line-left"></span>
            <span class="menu-icon__line"></span>
            <span class="menu-icon__line menu-icon__line-right"></span>
        </div>
        <div class="menu-header flex flex-row xs:col-span-full">
        <ul class="menu  text-sm flex-auto">
                <li><a href="/">{{__('Home page')}}</a></li>
                <li><a href="/menu">{{__('Menu page')}}</a></li>
                <li><a href="/booking">{{__('Booking page')}}</a></li>
                <li><a href="/feedbacks">{{__('Feedbacks')}}</a></li>
                <li><a href="/rules">{{__('Rules')}}</a></li>
                <li><a href="/vacancy">{{__('Vacancy page')}}</a></li>
            </ul>


        <div class="text-sm px-4 flex-auto">
            <a href="https://www.google.com.ua/maps/place/{{$place->address}},{{$place->city}}">
                <span>{{$place->address}}</span>
                <a class="phone sm:hidden px-4" href="tel:0684442454">068 444 24 54</a>
            </a>
        </div>
        <div class="langs flex-auto">
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