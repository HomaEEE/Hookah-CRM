<section class="home-footer p-z">
    <div class="container mx-auto px-2">

        <div class="logo p-8 flex flex-col md:flex-row gap-10 items-top">
        <div class="w-full md:flex-1 justify-left md:justify-start">
            <img class="h-[5vh]" src="{{ asset('assets/imgs/new-logo.svg') }}" alt="{{ __('Logo') }}">
        </div>

        <div class="w-full md:flex-1">
            <p>Noire Lounge Bar — це не просто кальян, це простір, де висока якість зустрічається з бездоганним сервісом.
                Ми звели приготування кальяну в ранг мистецтва: у нашій колекції зібрано найкращі бренди тютюну та авторські мікси.
                Доповніть ваш вечір авторськими коктейлями від нашого бармена та вишуканими стравами.
                Шукаєте ідеальне місце для зустрічі з друзями, романтичного побачення чи відпочинку в Одесі?
                Ласкаво просимо до Noire. Зручна локація, стильний інтер'єр та правильний вайб
            </p>
        </div>

    </div>

        {{-- <div class="top pb-20">
            <div class="flex items-stretch">
                <div class="flex-1">{{ $place->time['day'] ?? '12:00 - 22:30' }}</div>
                <div class="flex-1 text-center">{{ $place->address ?? 'Nina Pestroy. 27' }}, {{ $place->city }}</div>
                <div class="flex-1 text-center"><a href="tel:{{ $place->phone }}" >{{ $place->phone }}</a></div>


            </div> --}}
    </div>

</section>