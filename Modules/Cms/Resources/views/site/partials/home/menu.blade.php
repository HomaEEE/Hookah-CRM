<section class="home-menu mb-32 mt-12 md:py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-10 items-center">

            <div class="col-span-2 md:col-span-2 title pr-0 md:pr-10">
                <h3 class="gradient-title">
                    {{ __('Home menu title') }}
                </h3>
            </div>

            <div class="item col-span-1 translate-y-8 mt-10 relative">
                <a href="/menu">
                <div class="title">
                    <span>{{ __('Kitchen') }}</span>
                </div>
                <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/20 to-transparent z-0 pointer-events-none"></div>
                <img src="{{ asset('assets/imgs/hero/kitchen.webp') }}" alt="{{ __('Kitchen') }}" class="w-full h-auto object-cover relative -z-10" /></a>
            </div>

            <div class="item col-span-1 translate-y-8 mt-6 relative">
                <a href="/menu#coctails">
                <div class="title">
                    <span>{{ __('Bar') }}</span>
                </div>
                <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/20 to-transparent z-0 pointer-events-none"></div>
                <img src="{{ asset('assets/imgs/hero/bar.webp') }}" alt="{{ __('Bar') }}" class="w-full h-auto object-cover relative -z-10" />
            </div>

        </div>
    </div>
</section>