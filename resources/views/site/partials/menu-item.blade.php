@if (!empty($item->getFirstMediaUrl('products')))


<div class="mx-auto max-w-md overflow-hidden rounded-xl bg-grey shadow-md md:max-w-2xl">
  <!-- [!code word:md:flex] -->
  <div class="md:flex">
    <!-- [!code word:md:shrink-0] -->
    <div class="md:shrink-0">
      <!-- [!code word:md:h-full] -->
      <!-- [!code word:md:w-48] -->
      <img
        class="h-48 w-full object-fit md:h-full md:w-48"
        src="{{$item->getFirstMediaUrl('products')}}"
        alt="{{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}" >

    </div>
    <div class="p-8">
      <div class="text-sm font-semibold tracking-wide text-indigo-500 uppercase">
        <h4>{{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}</h4>
      </div>

      <p class="mt-2 text-gray-500">
 @if (!empty($item['frontend_desc']))
       {{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}
      @endif      </p>
    </div>
  </div>
</div>

    <div class="col-xs">
  <div class="menu-item box"  data-id="{{ $item->id }}">
  <div class="card-info">
    <div class="content">
      <h4>{{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}</h4>
      @if (!empty($item['frontend_desc']))
       <p>{{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}</p>
      @endif
      <div class="stats">
        <div>
          {{number_format($item['price'], 0)}} ₴
        </div>
        {{-- <div>
          {{number_format($item['out'], 0)}} <small>gr</small>
        </div> --}}
        <div>
          {{$item['sup']}}
        </div>
      </div>
    </div>
  </div>
  <div class="card-image">
        <img src="{{$item->getFirstMediaUrl('products')}}">
    <div class="image-overlay"></div>
  </div>
</div>
</div>

@else
    <div class="col-xs">
    <div class="menu-item box">
    <div class="card-info">
      <div class="content">
        <p class="title">{{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}</p>
        @if (!empty($item['frontend_desc']))
         <p class="description">{{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}</p>
        @endif
        <div class="stats">
            <div>
                {{number_format($item['price'], 0)}} ₴
              </div>
              {{-- <div>
                {{number_format($item['out'], 0)}} <small>gr</small>
              </div> --}}
              <div>
                {{$item['sup']}}
              </div>
        </div>
      </div>
    </div>

  </div>

  </div>
  @endif