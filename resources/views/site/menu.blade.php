@extends('layouts.site')
@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
    <link rel="stylesheet" href="/assets/css/style.css">
@endpush
@section('page', __('Menu page'))

@section('content')

<div class="">
    @include('site.properties', ['address' => 1, 'wifi' => 1, 'feedback'=> 1])

    <div class="flex-wrap">
        <div class="col-xs-12">
            <div class="tabs flex flex-nowrap">
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
    </div>
    <div class="row">
        <div class="col-xs-12 list">
            @foreach ($categories as $category)
            <div class="row">
                <div class="positions {{ $loop->index == 0 ? 'active' : '' }} @isset( $category->class['gram-hidden'] ) {{'gram-hidden'}}@endisset"
                id="{{$category['slug']}}">
                    <!--h3>{{$category['frontend_name'] ? $category['frontend_name'] : $category['category_name'] }}</!--h3-->

                    @foreach ($category->products as $item)
                        @include('site.partials.menu-item' , ['item'=> $item] )
                    @endforeach

                    @if(isset($category->childs))

                        @foreach($category->childs as $subcategory)
                            <h3>{{$subcategory['frontend_name'] ? $subcategory['frontend_name'] : $subcategory['category_name'] }}</h3>
                                @foreach ($subcategory->products as $item)
                                    @include('site.partials.menu-item' , ['item'=> $item] )
                                @endforeach
                        @endforeach
                    @endif
                     </div>
                    </div>
            @endforeach

    </div>
</div>
</div>

            @include('site.partials.modal' , ['modal'=> 'item'] )
            @include('site.partials.modal' , ['modal'=> 'feedback'] )
        <div class="modal-overlay"></div>


@endsection

@push('scripts')
    @vite(['resources/js/tab-menu.js', 'resources/js/modal.js'])
@endpush

