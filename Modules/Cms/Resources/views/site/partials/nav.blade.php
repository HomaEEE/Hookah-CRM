<header class="absolute inset-x-0 top-0 z-50">

<div class="container mx-auto px-4 flex items-center justify-between p-8">
        <div class="flex-1 logo">
                <h1>
                <a href="/">
                    <img class="w-32 h-auto" src="{{asset ('assets/imgs/new-logo.svg')}}" alt="{{ config('app.site') }}" width="128" height="43">
                </a>
            </h1>
        </div>

        <div class="burger">
            <div class="menu-icon" id="menu-btn">
                <span class="menu-icon__line menu-icon__line-left"></span>
                <span class="menu-icon__line"></span>
                <span class="menu-icon__line menu-icon__line-right"></span>
            </div>
        </div>
    </div>
</header>

<div id="full-menu" class="fixed inset-0 z-50 hidden opacity-0">

    <div class="lines hidden md:block">
        <div class="line-vertical  hidden md:block"></div>
        <div class="line-horizontal hidden md:block"></div>
        <div class="line-horizontal-short hidden md:block"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 h-full w-full relative z-10">

        <div class="flex flex-col md:h-full">
            <div class="flex flex-col md:h-[75vh] p-10 md:px-24 md:py-20 space-y-8 md:space-y-10">
                <li class="nav-menu-item"><a href="/menu">{{ __('Menu page') }}</a></li>
                <li class="nav-menu-item"><a href="/feedbacks">{{ __('Feedbacks') }}</a></li>
                <li class="nav-menu-item"><a href="/vacancy">{{ __('Vacancies') }}</a></li>
                <li class="nav-menu-item"><a href="/rules">{{ __('Rules') }}</a></li>
            </div>

            <div class="md:h-[25vh] py-10 px-10 md:px-24 md:py-12 flex content-fade">
                <div class="grid grid-cols-2 w-full gap-4">
                    <div>{{ __('Contacts') }}</div>
                    <div>
                        <span class="block"><a href="tel:{{ $place->phone }}" >{{ $place->phone }}</a></span>
                        <span class="block"><a href="{{ $place->instagram }}">instagram</a></span>
                        <span class="block"><a href="{{ $place->telegram }}">telegram</a></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:h-full">
            <div class="h-[75vh] px-10 sm:px-24 sm:py-18 flex items-center hidden sm:block">
                <div class="h2 md:py-4 md:px-0 content-fade">{{ __('Reserve table') }}</div>

                <form class="nav-form space-y-4 pt-12 pr-24 max-w-md w-full content-fade">
                   <div class="input-group">
                        <label for="name">{{ __('Name') }}</label>
                        <input type="text" placeholder="{{ __('Name') }}" id="name" autocomplete="on"/>
                    </div>

                    <div class="input-group">
                        <label for="phone">{{ __('Phone') }}</label>
                        <input type="tel" placeholder="{{ __('Phone') }}" id="phone" autocomplete="on" />
                    </div>

                    <div class="input-group">
                        <label for="date">{{ __('Date') }}</label>
                        <input type="text" placeholder="{{ __('Date') }}" id="date" />
                    </div>

                    <div class="input-group">
                        <label for="guests">{{ __('Guests') }}</label>
                        <input type="text" placeholder="{{ __('Guests') }}" id="guests" />
                    </div>

                    <div class="input-group">
                        <label for="duration">{{ __('Duration') }}</label>
                        <input type="text" placeholder="{{ __('Duration') }}" id="duration" />
                    </div>
                    <div class="text-right mt-2">
                        <button type="submit" class="text-sm font-light hover:text-white text-white/70 transition-colors">Отправить</button>
                    </div>
                </form>

            </div>

            <div class="md:h-[20vh] py-8 px-10 md:px-24 md:py-4 flex items-center content-fade">
                <div class="grid grid-cols-2 w-full gap-4">
                    <div>{{ __('Address') }}</div>
                    <div>
                        <span class="block">{{ $place->city }}, UA</span>
                        <span class="block">{{ $place->address ?? 'Nina Pestroy. 27' }}</span>
                        <span class="block">{{ $place->time['day'] ?? '12:00 - 22:30' }}</span>
                    </div>
                </div>
            </div>

            <div class="w-full p-8 md:hidden">
                <a class="reserve btn" href="/booking">{{ __('Booking') }}</a>
            </div>
        </div>
        <div id="cross-col" class="flex flex-col bg-amber-50">
            <span></span>
         </div>

    </div>
</div>