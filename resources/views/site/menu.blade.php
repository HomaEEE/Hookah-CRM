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

<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-3">
        <div class="tabs">
            <ul>
            @foreach ($categories as $cat)
            <li data-target="{{ $cat['slug'] }}"
                class="tab-item {{ $loop->index == 0 ? 'active' : '' }}">
                {{$cat['frontend_menu_name']}}
            </li>
            @endforeach
            </ul>
        </div>
        </div>

            <div class="col-xs-12 col-md-9 list">
                @foreach ($categories as $category)
                <div class="positions {{ $loop->index == 0 ? 'active' : '' }} @isset( $category->class['gram-hidden'] ) {{'gram-hidden'}}@endisset"
                id="{{$category['slug']}}">
                    <!--h3>{{$category['frontend_name'] ? $category['frontend_name'] : $category['category_name'] }}</!--h3-->

                    @foreach ($category->products as $item)
                    <div class="menu-item">
                        <div class="item">
                            <div class="title">
                                {{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}
                            </div>
                            <div class="property">
                                <p class="sup">{{$item['sup']}}</p>
                                <!--p class="gram">{{number_format($item['out'], 0)}} gr</p-->
                                <p class="price">{{number_format($item['price'], 0)}} ₴</p>
                            </div>
                        </div>
                        @if (isset($item['frontend_desc']))
                        <div class="description">
                            <p>{{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}</p>
                            @if (!empty($item->getFirstMediaUrl('products')))
                                 @if (!empty($item->getFirstMediaUrl('products')))
                                            <a data-fancybox href="{{$item->getFirstMediaUrl('products')}}"
                                            data-caption=" {{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}">
                                            <img src="{{$item->getFirstMediaUrl('products')}}">
                                            </a>
                                        @endif
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach

                    @if(isset($category->childs))

                        @foreach($category->childs as $subcategory)
                            <h3>{{$subcategory['frontend_name'] ? $subcategory['frontend_name'] : $subcategory['category_name'] }}</h3>
                                @foreach ($subcategory->products as $item)

                                <div class="menu-item">
                                    <div class="item">
                                        <div class="title"> {{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}</div>
                                        <div class="property">
                                            <p class="sup">{{$item['sup']}}</p>
                                <!--p class="gram">{{number_format($item['out'], 0)}} gr</p-->
                                            <p class="price">{{number_format($item['price'], 0)}} ₴</p>
                                        </div>
                                    </div>
                                  @if (isset($item['frontend_desc']))
                                    <div class="description">
                                        <p>{{$item['frontend_desc'] ? $item['frontend_desc'] : ""}}</p>
                                        @if (!empty($item->getFirstMediaUrl('products')))
                                            <a data-fancybox href="{{$item->getFirstMediaUrl('products')}}"
                                            data-caption=" {{$item['frontend_name'] ? $item['frontend_name'] : $item['product_name'] }}">
                                            <img src="{{$item->getFirstMediaUrl('products')}}">
                                            </a>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                        @endforeach
                    @endif
                     </div>
                @endforeach

        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paroller.js/1.4.7/jquery.paroller.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<script src="/assets/js/odessa.js"></script>
<script src="/assets/js/menu.js"></script>

@endpush
