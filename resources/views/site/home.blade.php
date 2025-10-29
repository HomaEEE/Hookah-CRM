@extends('layouts.site') @push('css') @endpush @section('page', __('Home page'))
@section('title', __('Home page')) @section('description', 'Описание страницы')
@push('scripts') @vite('resources/js/swiper.js') @endpush @section('content')

<div class="container py-4 mx-auto">
	<div class="hero">
		<div class="hero-image aspect-5/6 md:aspect-7/4 xl:aspect-7/5 object-cover rounded-4xl">
				<img class="h-full object-cover md:h-full inset-y-0 right-0" src="{{ asset('assets/imgs/hero/1.webp') }}" alt="">
		</div>

		<section class="links">
			<li>
				<a href="/menu">{{ __("Menu") }}</a>
			</li>
			<li>
				<a href="/booking">{{ __("Booking") }}</a>
			</li>
			<li>
				<a href="/rules">{{ __("Rules") }}</a>
			</li>
		</section>
	</div>
</div>

<div class="relative flex overflow-x-hidden marquee -z-1 m-0 pt-4 pb-12 rounded-t-2xl">
  <div class="animate-marquee  whitespace-nowrap">
 		<span class="mx-4 text-xl">{{ __("Slogan") }}</span>
		<span class="mx-4 text-xl">{{ __("Slogan") }}</span>
		<span class="mx-4 text-xl">{{ __("Slogan") }}</span>
		<span class="mx-4 text-xl">{{ __("Slogan") }}</span>
		<span class="mx-4 text-xl">{{ __("Slogan") }}</span>

</div>

  <div class="absolute animate-marquee2  whitespace-nowrap">
  		<span class="mx-4 text-xl">{{ __("Slogan") }}</span>
		<span class="mx-4 text-xl">{{ __("Slogan") }}</span>
		<span class="mx-4 text-xl">{{ __("Slogan") }}</span>
		<span class="mx-4 text-xl">{{ __("Slogan") }}</span>
		<span class="mx-4 text-xl">{{ __("Slogan") }}</span>
  </div>
</div>

<div class="about p-40 -mt-5 z-2 rounded-t-2xl">
	<div class="container">

			<h2 class="text-center">ДостатьО ДЕКІЛЬКА ХВИЛИН, щоб Обрати саме нас</h2>

		<div class="grid grid-cols-1 sm:grid-cols-2 md:divide-x divide-[#535353]">
				<div class="pt-4 md:p-4">
					<p>
						Наша команда ставиться до кальянної справи, як до мистецтва, ми
						стежимо за якістю нашої продукції та робимо все можливе, щоб ваш
						візит до нас став незабутнім!
					</p>
				</div>

				<div class="pt-4 md:p-4 md:pt-32">
					<p>
						Дуже тісно знайомі з продукцією брендів, знаємо, що можна
						запропонувати новачку, а також приємно здивувати професіонала.
					</p>
				</div>
			</div>
		</div>
</div>

<div class="booking p-40 -mt-5 z-3 rounded-t-2xl">
	<div class="container">
		<h2>{{ __("Booking") }}</h2>
	</div>
</div>

@endsection @push('scripts') @vite('resources/js/swiper.js') @endpush
