@if (!empty($item->getFirstMediaUrl('products')))


<div class="flex max-w-xs items-center gap-x-4 overflow-hidden rounded-xl  md:max-w-2xl menu-item" data-id="{{ $item->id }}">
  <div class="flex">
    <div class="shrink-0">
      <img
        class="size-24 md:size-32 lg:size-48 object-fit shrink-0"
        src="{{$item->getFirstMediaUrl('products')}}"
        alt="{{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}" >

    </div>
    <div class="p-8  gap-x-4 ">
      <div class="text-sm font-semibold tracking-wide uppercase">
        <h4>{{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}</h4>
      </div>

      <p class="mt-2">
          @if (!empty($item['frontend_desc']))
              {{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}
            @endif
      </p class="property">
        <div>
          {{number_format($item['price'], 0)}} ₴
        </div>

        <div>
          {{number_format($item['out'], 0)}} <small>{{ __('gr') }}</small>
        </div>
        <div>
          {{$item['sup']}}
        </div>
      <p>
      </p>
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