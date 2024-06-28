<div class="menu-item">
@if (!empty($item->getFirstMediaUrl('products')))
<div class="row item">
    <div class="col-xs-12 col-md-9">
        <div class="title">
            {{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}
            <p class="sup">{{$item['sup']}}</p>
        </div>
    </div>
    <div class="col-xs">
     @if (!empty($item['frontend_desc']))
        <div class="description">
            {{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}
        </div>
        <!--p class="gram">{{number_format($item['out'], 0)}} gr</p-->
        @endif
        <p class="price">{{number_format($item['price'], 0)}}₴</p>
    </div>
    <div class="col-xs-4 col-sm-4">
        <div class="img">
        <a data-fancybox href="{{$item->getFirstMediaUrl('products')}}"
        data-caption=" {{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}">
        <img src="{{$item->getFirstMediaUrl('products')}}">
        </a>
        </div>
    </div>
</div>
@else
<div class="row item">
    <div class="col-xs-8">
        <div class="title">
            {{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}
        </div>
    </div>

    @if (!empty($item['sup']))
    <div class="col-xs-2 col-sm-2">
            <p class="sup">{{$item['sup']}}</p>
    </div>
    @endif

    <div class="col-xs-2 end-xs">
         <p class="price">{{number_format($item['price'], 0)}}₴</p>
    </div>

    @if (!empty($item['frontend_desc']))

    <div class="col-xs-12">
        <div class="description">
            {{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}
        </div>
        <!--p class="gram">{{number_format($item['out'], 0)}} gr</p-->
        </div>
    @endif
</div>
@endif
</div>
