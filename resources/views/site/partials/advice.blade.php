<div class="advice hidden md:inline p-4 mb-4 mx-auto
             grid grid-cols-1 md:grid-cols-3
             gap-4 md:gap-6
             divide-y divide-[#535353] md:divide-y-0 md:divide-x">

    <!-- 1‑я колонка (контакты) -->
    <div class="p-2 border-b border-[#535353] md:border-b-0 md:border-r md:border-[#535353]">
        <p><i class="icon-mobile"></i> <a class="tel" href="tel:{{ $place->phone }}">{{ $place->phone }}</a></p>
        <p><i class="icon-clock"></i> {{ $place->time['day'] }}</p>
        <p><i class="icon-instagram"></i> <a href="https://instagram.com/{{ $place->instagram }}" target="_blank">{{ $place->instagram }}</a></p>
    </div>

    <!-- 2‑я колонка (свойства) -->
    <div class="p-2 border-b border-[#535353] md:border-b-0 md:border-r md:border-[#535353]">
        <p><i class="icon-wifi"></i> {{ $place->properties['wifi'] }}</p>
        <p><i class="icon-key"></i> {{ $place->properties['password'] }}</p>
        <p><i class="icon-instagram"></i> <a href="https://instagram.com/{{ $place->instagram }}" target="_blank">{{ $place->instagram }}</a></p>
    </div>

    <!-- 3‑я колонка (комментарий) -->
    <div class="comment p-2 col-span-1 md:col-span-1" id="comment">
        <h4><i class="icon-comment"></i> {{ __('Feedback button') }}</h4>
        <p>{{ __('Feedback comment') }}</p>
    </div>

</div>