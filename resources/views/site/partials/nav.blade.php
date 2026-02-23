<header class="container mx-auto py-8 px-4 md:p-4">
    <div class="flex flex-row ">
        <!-- ЛОГО -->
        <div class="basis-256">
                <h1>
                <a href="/">
                    <img class="w-32" src="/assets/imgs/logo.svg" alt="Noire hookah Lounge">
                </a>
            </h1>
        </div>



        <!-- БУРГЕР -->
        <div class="basis-32">
        <div class=" menu-icon">
            <span class="menu-icon__line menu-icon__line-left"></span>
            <span class="menu-icon__line"></span>
            <span class="menu-icon__line menu-icon__line-right"></span>
        </div>
        </div>

        <!-- НАВИГАЦИЯ -->
        <div class="menu-header block md:flex flex-row items-center">

            <!-- Меню -->
            <ul class="menu  md:flex-row md:space-x-6 text-center md:text-left">
                <li><a href="/">{{ __('Home page') }}</a></li>
                <li><a href="/menu">{{ __('Menu page') }}</a></li>
                <li><a href="/booking">{{ __('Booking page') }}</a></li>
                <li><a href="/feedbacks">{{ __('Feedbacks') }}</a></li>
                <li><a href="/rules">{{ __('Rules') }}</a></li>
                <li><a href="/vacancy">{{ __('Vacancy page') }}</a></li>
            </ul>

            <!-- Адрес + телефон -->
            <div class="text-sm mt-4 md:mt-0 md:px-4 text-left">
                <a href="https://www.google.com.ua/maps/place/{{$place->address}},{{$place->city}}" class="block">
                    <span>{{ $place->address }}</span>
                </a>
                <a class="phone sm:hidden block mt-2 md:mt-0" href="tel:0684442454">068 444 24 54</a>
            </div>

            <!-- Языки -->
            <div class="langs mt-4 md:mt-0 flex justify-center md:justify-start space-x-2 text-sm">
                @foreach (Config::get('app.languages_frontend') as $lang => $language)
                    <a href="{{ route('lang.switch', $lang) }}"
                       class="@if($lang == App::getLocale()) active @else @endif">
                        {{ $language }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</header>