@if (!empty($item->getFirstMediaUrl('products')))

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