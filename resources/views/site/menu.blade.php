@extends('layouts.site') @push('css')
<link rel="stylesheet" href="/assets/css/style.css" />
@endpush @section('page', __('Menu page')) @section('content')

<div class="container mx-auto py-4 md:p-2">
        <h2 class="divider line razor title">{{__('Menu')}}</h2>
        <div class="flex flex-row items-center">
            <div class="tabs">
                <div class="menu-toggle">{{ __('Menu') }} <span>+</span></div>
                <ul class="tabs-links">
                    @foreach ($categories as $cat)
                        <a href="#{{ $cat['slug'] }}"><li>{{ $cat["frontend_menu_name"] }}</li></a>
                    @endforeach
                </ul>
            </div>
        </div>
</div>




@foreach ($categories as $category)
    <div class="flex flex-col md:flex-row h-auto border-y border-zinc-500 @isset($category->class['gram-hidden']) gram-hidden @endisset" id="{{ $category['slug'] }}">

        <aside class="w-full md:w-[30%] border-b md:border-r border-zinc-500 flex justify-center p-4 sm:p-8 relative">
            <div class="sticky top-10 bottom-12 h-fit">
                <div class="h3" id="{{ $category['slug'] }}">
                    {{ $category['frontend_name'] }}
                </div>
            </div>
        </aside>

        <div class="w-full md:w-[70%] flex flex-col">

            @if($category->products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 divide-y-4 divide-solid divide-zinc-500">
                    @foreach ($category->products as $item)
                        @include('site.partials.menu-item', ['item' => $item])
                    @endforeach
                </div>
            @endif

            @if(isset($category->childs))
                @foreach($category->childs as $subcategory)

                    <div class="w-full py-4 border-b border-zinc-500 text-center" id="{{ $subcategory['id'] }}">
                        <div class="h4 uppercase">
                            {{ $subcategory["frontend_name"] ?: $subcategory["category_name"] }}
                        </div>
                    </div>

                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 border-none md:border-b border-zinc-500">
                        @foreach ($subcategory->products as $item)
                            @include('site.partials.menu-item', ['item' => $item])
                        @endforeach
                    </div>

                @endforeach
            @endif

        </div>
    </div>
@endforeach


@include('site.partials.modal' , ['modal'=> 'item'] )
@include('site.partials.modal' , ['modal'=> 'feedback'] )

<div class="modal-overlay"></div>

@endsection
