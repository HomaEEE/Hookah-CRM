@extends('layouts.site')
@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"/>
    <link rel="stylesheet" href="/assets/css/style.css">
@endpush
@section('page', __('Menu page'))

@section('content')

<div class="container">
    @include('site.properties', ['address' => 1, 'wifi' => 1, 'feedback'=> 1])

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
            @endforeach

    </div>
</div>
</div>
{{--
<div class="modal">
    <div class="content">

    </div>
</div> --}}
@endsection

@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

<script src="/assets/js/odessa.js"></script>
<script src="/assets/js/menu.js"></script>

@endpush
