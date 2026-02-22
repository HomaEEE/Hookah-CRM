<div class="grid grid-cols-1 ">
    @php
        $imageUrl = $item->getFirstMediaUrl('products');
        $title = $item['frontend_name'] ?: $item['product_name'];
        $description = $item['frontend_desc'] ?? '';
    @endphp

    <div class="flex p-4 gap-4 border-r border-b border-zinc-500 menu-item"
        @if ($imageUrl)
            data-id="{{ $item->id }}"
            data-title="{{ $title }}"
            data-sup="{{ $item['sup'] }}"
            data-desc="{{ $description }}"
            data-price="{{ number_format($item['price'], 0) }} ₴"
            data-image-url="{{ $imageUrl }}"
        @endif
    >
     @if ($imageUrl)
        <div class="w-24 h-24 shrink-0 overflow-hidden rounded-sm">
            <div class="shrink-0 rounded-xl image">
                @if(!empty($item['new']))
                    <p class="sup">NEW</p>
                @endif
                    <img src="{{ $imageUrl }}"
                         alt="{{ $title }}"
                         loading="lazy"
                         class="w-full h-full object-cover">
            </div>

        </div>
      @endif
        <div class="flex flex-col justify-between">
            <div>
                <div class="text-[13px] uppercase mb-1">{{ $title }}
                   @if(!empty($item['sup']))
                        {{ $item['sup'] }}
                   @endif


              </div>
                @if(!empty($description))
                    <div class="text-[10px] text-zinc-400">
                        {{ $description }}
                    </div>
                @endif
            </div>

            <div class="flex justify-between items-end mt-2">
                <span class="text-[13px]">{{ number_format($item['price'], 0) }} ₴</span>
            </div>
        </div>
    </div>
</div>