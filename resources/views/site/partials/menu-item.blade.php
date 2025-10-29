@if (!empty($item->getFirstMediaUrl('products')))


<div class="mx-auto mb-4 max-w-md overflow-hidden rounded-xl shadow-md md:max-w-2xl menu-item" data-id="{{ $item->id }}">
  <div class="flex">
    <div class="shrink-0">
      <img
        class="h-32 w-32 object-cover md:h-full md:w-48"
        src="{{$item->getFirstMediaUrl('products')}}"
        alt="{{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}" />
    </div>
    <div class="p-2">
      <h4 class="text-sm font-semibold tracking-wide uppercase">{{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}</h4>

         @if (!empty($item['frontend_desc']))
         <p class="mt-2 description">
              {{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}
          </p>
            @endif
      <div class="property t-2 pt-2 columns-3">
        <div>
          <p>{{number_format($item['price'], 0)}} ₴</p>
        </div>

        <div>
          <p>{{number_format($item['out'], 0)}} <small>{{ __('gr') }}</small></p>
        </div>

        <div>
          <p>{{$item['sup']}}</p>
        </div>

      </div>
    </div>
  </div>
</div>

@else

<div class="mx-auto mb-4 max-w-md rounded-xl shadow-md md:max-w-2xl menu-item" data-id="{{ $item->id }}">
  <div class="flex">

    <div class="p-4">
      <h4 class="text-sm uppercase">{{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}</h4>

         @if (!empty($item['frontend_desc']))
         <p class="mt-2 description">
              {{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}
          </p>
            @endif
      <div class="property t-2 pt-2 columns-3">
        <div>
          <p>{{number_format($item['price'], 0)}} ₴</p>
        </div>

        <div>
          <p>{{number_format($item['out'], 0)}} <small>{{ __('gr') }}</small></p>
        </div>

        <div>
          <p>{{$item['sup']}}</p>
        </div>

      </div>
    </div>
  </div>
</div>

  @endif