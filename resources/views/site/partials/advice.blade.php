<div class="advice grid  grid-cols-2 md:grid-cols-3 gap-0 md:gap-4 p-4 mb-4">

    <!-- 1‑я колонка (контакты) -->
    <div class="p-3 col-span-1 md:col-span-1 ">
        <p><i class="icon-mobile"></i> <a class="tel" href="tel:{{ $place->phone }}">{{ $place->phone }}</a></p>
        <p><i class="icon-clock"></i> {{ $place->time['day'] }}</p>
        <p><i class="icon-instagram"></i> <a href="https://instagram.com/{{ $place->instagram }}" target="_blank">{{ $place->instagram }}</a></p>
    </div>

    <!-- 2‑я колонка (свойства) -->
    <div class="p-3 col-span-1 md:col-span-1 border-l-1 md:border-x">
        <p><i class="icon-wifi"></i> {{ $place->properties['wifi'] }}</p>
        <p><i class="icon-key"></i> {{ $place->properties['password'] }}</p>
        <p><i class="icon-instagram"></i> <a href="https://instagram.com/{{ $place->instagram }}" target="_blank">{{ $place->instagram }}</a></p>
    </div>

    <!-- 3‑я колонка (комментарий) -->
    <div class="comment mt-0 p-2 col-span-2 md:col-span-1 border-t-1 md:border-none" id="comment">
        <h4><i class="icon-comment"></i> {{ __('Feedback button') }}</h4>
        <p>{{ __('Feedback comment') }}</p>
    </div>

</div>