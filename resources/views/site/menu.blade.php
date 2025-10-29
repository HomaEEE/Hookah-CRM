@extends('layouts.site') @push('css')
<link
	rel="stylesheet"
	href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<link rel="stylesheet" href="/assets/css/style.css" />
@endpush @section('page', __('Menu page')) @section('content')

<div class="container">
	    @include('site.partials.advice', ['address' => 1, 'wifi' => 1, 'feedback'=>1])

        <div class="flex flex-row items-center">
            <div class="tabs flex flex-nowrap">
                <ul>
                    @foreach ($categories as $cat)
                    <li data-target="{{ $cat['slug'] }}"
                        class="tab-item {{ $loop->index == 0 ? 'active' : '' }}">
                        {{ $cat["frontend_menu_name"] }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        @foreach ($categories as $category)
                <div class="flex flex-row positions pb-4 {{ $loop->index == 0 ? 'active' : '' }} @isset( $category->class['gram-hidden'] ) {{ 'gram-hidden' }} @endisset" id="{{ $category['slug'] }}">

                    @foreach ($category->products as $item)
                    @include('site.partials.menu-item' , ['item'=> $item] )
                    @endforeach
                    @if(isset($category->childs))
                    @foreach($category->childs as $subcategory)
                    <h3 class="pt-8 pb-2 text-xl" id="{{ $subcategory['id'] }}">
                        {{
                            $subcategory["frontend_name"]
                                ? $subcategory["frontend_name"]
                                : $subcategory["category_name"]
                        }}
                    </h3>
                    @foreach ($subcategory->products as $item)
                    @include('site.partials.menu-item' , ['item'=> $item] ) @endforeach
                    @endforeach @endif
                </div>
		@endforeach
</div>

@include('site.partials.modal' , ['modal'=> 'item'] )
@include('site.partials.modal' , ['modal'=> 'feedback'] )
<div class="modal-overlay"></div>

@endsection @push('scripts') @vite(['resources/js/tab-menu.js',
'resources/js/modal.js']) @endpush
