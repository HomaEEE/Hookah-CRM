@extends('layouts.site') @push('css')
<link rel="stylesheet" href="/assets/css/style.css" />
@endpush @section('page', __('Menu page')) @section('content')

<div class="container mx-auto py-4 md:p-2">
        <h2 class="divider line razor title">{{__('Menu')}}</h2>
        <div class="flex flex-row items-center py-6">
            <div class="tabs">
                <div class="menu-toggle">Menu +</div>
                <ul>
                    @foreach ($categories as $cat)
                    <li id="{{ $cat['slug'] }}">
                        {{ $cat["frontend_menu_name"] }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
</div>


@foreach ($categories as $category)
    {{-- Основной контейнер секции: 100% высоты экрана для каждой категории --}}
    <div class="flex flex-col md:flex-row min-h-screen border-y border-zinc-500 @isset($category->class['gram-hidden']) gram-hidden @endisset" id="{{ $category['slug'] }}">

        <aside class="w-full md:w-[30%] border-b md:border-b-0 md:border-r border-zinc-500 flex justify-center p-12 xs:p-8 relative">
            <div class="sticky top-10 bottom-12 h-fit">
                <h2 class="text-5xl font-black uppercase tracking-tighter text-white" id="{{ $category['frontend_name'] }}">
                    {{ $category['frontend_name'] }}
                </h2>
            </div>
        </aside>

        <div class="w-full md:w-[70%] flex flex-col xs:bg-zinc-900 bg-none">

            {{-- 1. Вывод товаров, принадлежащих напрямую основной категории --}}
            @if($category->products->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2">
                    @foreach ($category->products as $item)
                        @include('site.partials.menu-item', ['item' => $item])
                    @endforeach
                </div>
            @endif

            {{-- 2. Вывод подкатегорий (например, Завтраки) --}}
            @if(isset($category->childs))
                @foreach($category->childs as $subcategory)

                    <div class="w-full py-4 border-b border-zinc-500 text-center bg-zinc-900/30" id="{{ $subcategory['id'] }}">
                        <div class="text-xl font-light uppercase tracking-[0.3em] text-white">
                            {{ $subcategory["frontend_name"] ?: $subcategory["category_name"] }}
                        </div>
                    </div>

                <div class="grid grid-cols-1 md:grid-cols-2">
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

@push('scripts')
    @vite(['resources/js/modal.js'])
@endpush
