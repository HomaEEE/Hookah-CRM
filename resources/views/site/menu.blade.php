@extends('layouts.site')
@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
    <link rel="stylesheet" href="/assets/css/style.css">
@endpush
@section('page', __('Menu page'))

@section('content')
<div class="menu-container">

    @include('site.nav')
    @include('site.properties')

        <div class="tabs">
            @foreach ($categories as $cat)
            <li class="menu-item"><a href="#{{$cat['slug']}}" class="menu-item {{ $loop->index == 0 ? 'active' : '' }}">{{$cat['frontend_menu_name']}}</a></li></li>
            @endforeach
        </div>


     @foreach ($categories as $category)
        <div class="col-xs-12 menu_category {{$category['slug']}}
        @isset( $category->class['gram-hidden'] ) {{'gram-hidden'}}@endisset" id="{{$category['slug']}}">
            @if (isset($category->class['logo']))
                <img src="{{$category->class['logo']}}" alt="" class="logo-category">
            @else
            <h2 class="menu_title">{{$category['frontend_name'] ? $category['frontend_name'] : $category['category_name'] }}</h2>
            @endif
        @if(isset($category->childs))
            @foreach($category->childs as $subcategory)
                <h3> {{$subcategory['frontend_name'] ? $subcategory['frontend_name'] : $subcategory['category_name'] }}</h3>
                @foreach ($subcategory->products as $item)

                    <div class="row menu_item">
                        <div class="col-xs-9 col-md-10 item-title">
                            <h4 class="menu_item">
                                {{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}
                                <sup>{{$item['sup']}}</sup>
                            </h4>
                            <p>
                                {{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}
                            </p>
                        </div>

                        <div class="col-xs-1 col-md-2 item-price">
                            <span>{{number_format($item['price'], 0)}}₴</span>
                            <small>{{number_format($item['out'], 0)}}gr</small>
                        </div>

                    </div>
                @endforeach
            @endforeach
        @endif

        @foreach ($category->products as $item)
        <div class="row menu_item">
            <div class="col-xs-8 col-md-10 item-title">
                <h4>
                    {{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}
                    <sup>{{$item['sup']}}</sup>
                </h4>
                <p>
                    {{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}
                </p>
            </div>
            <div class="col-xs-2 col-md-1 item-price">
                <span>{{number_format($item['price'], 0)}}₴</span>
                <small>{{number_format($item['out'], 0)}}gr</small>

            </div>

            <div class="col-xs-1 col-md-1 item-image center-xs">
                @if (!empty($item->getFirstMediaUrl('products')))
                <a data-fancybox data-src="{{$item->getFirstMediaUrl('products')}}"
                    data-caption=" {{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}">
                    <i class="icon-picture"></i>
                </a>

                @endif
            </div>
        </div>
    @endforeach
        @if (isset($category->class['img']))
            <img src="{{$category->class['img']}}" alt="" class="bg-image">
        @endif
        @if (isset($category->class['item']))
            <img src="{{$category->class['item']}}" alt="" class="item-image">
        @endif
        </div>
     @endforeach
@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paroller.js/1.4.7/jquery.paroller.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<script src="/assets/js/odessa.js"></script>
<script src="/assets/js/menu.js"></script>

@endpush
