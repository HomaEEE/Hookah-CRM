<section class="home-footer pt-4">
    <div class="container mx-auto px-4">
        <div class="top pb-20">
            <div class="flex">
                <div class="flex-1">{{ $place->time['day'] ?? '12:00 - 22:30' }}</div>
                <div class="flex-1 text-center">{{ $place->address ?? 'Nina Pestroy. 27' }}, {{ $place->city }}</div>
                <div class="flex-1 text-center"><a href="tel:{{ $place->phone }}" >{{ $place->phone }}</a></div>

            <div class="flex-1 text-right social">
                <a href=""><i class="icon-instagram"></i></a>
                <a href=""><i class="icon-facebook-squared"></i></a>
                <a href=""><i class="icon-wifi"></i></a>
            </div>
            </div>
    </div>
    <div class="logo pb-8">
        <img src="{{ asset('assets/imgs/logo.svg') }}" alt="{{ __('Logo') }}">
    </div>
</section>