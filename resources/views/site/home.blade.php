@extends('layouts.site') @push('css')


@endpush
@section('page', __('Home page'))
@section('title', __('Home page'))
@section('description', 'Описание страницы')

@push('scripts')
    @vite('resources/js/swiper.js')
@endpush


@section('content')
<div data-scroll-section class="page-container">
	<div class="hero">
	<div class="container">

		 <div class="swiper">
				<div class="swiper-wrapper">
					<div class="swiper-slide">
						<span class="text" data-swiper-parallax="-200">
							<h2>{{ __("Hookahs") }}</h2>
                            <h4>
                                Широкий вибір смаків
                            </h4>
                            <p>від класичних до екзотичних фруктових та десертних.
                            </p>
						</span>
						<img src="{{ asset('assets/imgs/hero/hookah.webp') }}" alt="" />
					</div>
					<div class="swiper-slide">
						<span class="text" data-swiper-parallax="-200">
							<h2>{{ __("Cocktails") }}</h2>
                            <h4>
                                Класичні коктейлі
                            </h4>
                            <h4>
                                Авторські коктейлі
                             </h4>
                            <h4>
                                Безалкогольні коктейлі
                            </h4>
						</span>
						<img src="{{ asset('assets/imgs/hero/coctails.png') }}" alt="" />
					</div>
					<div class="swiper-slide">
						<span class="text" data-swiper-parallax="-200">
							<h2>{{ __("Kitchen") }}</h2>
                            <h4>Закуски</h4>
                            <h4>Салати</h4>
                            <h4>Гарячі страви </h4>
                            <h4>Бургери </h4>
						</span>
						<img src="{{ asset('assets/imgs/hero/hookah.webp') }}" alt="" />
					</div>
			</div>
			<div class="swiper-pagination"></div>
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

	<div class="about">
		<div class="container">
			<div class="row">
				<h2 class="title">ДостатьО ДЕКІЛЬКА ХВИЛИН, щоб Обрати саме нас</h2>

				<div class="inner">
					<div class="box">
						<p>
							Наша команда ставиться до кальянної справи, як до мистецтва, ми
							стежимо за якістю нашої продукції та робимо все можливе, щоб ваш
							візит до нас став незабутнім!
						</p>
						<h3>Дим, що надихає</h3>
					</div>

					<div class="box">
						<p class="right">
							Дуже тісно знайомі з продукцією брендів, знаємо, що можна
							запропонувати новачку, а також приємно здивувати професіонала.
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="booking block">
		<div class="container">
		<h2>{{ __("Booking") }}</h2>
		</div>
    </div>

    </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/swiper.js')
@endpush

