@if (!empty($item->getFirstMediaUrl('products')))


<div class="mx-auto mb-4 max-w-md overflow-hidden rounded-xl shadow-md md:max-w-2xl menu-item relative p-4"
 data-id="{{ $item->id }}"
     data-title="{{ $item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}"
     data-sup="{{ $item['sup'] }}"
     data-desc="{{ $item['frontend_desc'] ? $item['frontend_desc'] : "" }}"
     data-price="{{ number_format($item['price'], 0) }} ₴"

     data-image-url="{{$item->getFirstMediaUrl('products')}}">
  <div class="flex">
    <div class="shrink-0 rounded-xl image">
    @if(!empty($item['sup']))
        <p class="sup">{{ $item['sup'] }}</p>
    @endif
      <img loading="lazy"
          class="h-32 w-32 object-cover md:h-full md:w-38"
          src="{{$item->getFirstMediaUrl('products')}}"
          alt="{{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}" />
      </div>
    <div class="p-2 md:px-2 w-full">
      <div class="grid">
  <h4 class=" font-semibold inline tracking-wide uppercase">{{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }} </h4>
      </div>
         @if (!empty($item['frontend_desc']))
         <p class="mt description">
              {{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}
          </p>
            @endif
      <div class="property t-2 pt-2 flex flex-row absolute inset-x w-full bottom-0 h-12">
        <div>
          <p>{{number_format($item['price'], 0)}} ₴</p>
        </div>

        <div>
          <!--p>{{number_format($item['out'], 0)}} <small>{{ __('gr') }}</small></p-->
        </div>

        <div>
          <!--p>{{$item['sup']}}</p-->
        </div>

      </div>
    </div>
  </div>
</div>

@else

<div class="mx-auto mb-4 max-w-md rounded-xl shadow-md md:max-w-2xl menu-item" data-id="{{ $item->id }}">
  <div class="flex">

  <div class="p-4 w-full">
      <div class="grid grid-cols-4 gap-4"">
  <h4 class=" font-semibold inline col-span-3 tracking-wide uppercase">{{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }} </h4>

        <p class="text-right"> {{$item['sup']}}</p>
      </div>
         @if (!empty($item['frontend_desc']))
         <p class="mt-2 description">
              {{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}
          </p>
            @endif
      <div class="property mt-2 pt-2 flex-row">
        <div >
          <p>{{number_format($item['price'], 0)}} ₴</p>
        </div>

        <div>
          <!--p>{{number_format($item['out'], 0)}} <small>{{ __('gr') }}</small></p-->
        </div>
      </div>
    </div>
  </div>
</div>

  @endif