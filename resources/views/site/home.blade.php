@extends('layouts.site') @push('css')
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
/>
<link rel="stylesheet" href="/assets/css/style.css" />
@endpush
@section('page', __('Home page'))
@section('content')
<div class="page-container">
	<div class="hero">
			<div class="swiper">
				<div class="swiper-wrapper">
					<div class="swiper-slide">
						<span class="text" data-swiper-parallax="-200">
							<h2>{{ __("Hookahs") }}</h2>
                            <h4>
                                Широкий вибір тютюну
                            </h4>
                            <p>від класичних смаків до екзотичних фруктових та десертних.
                            </p>
						</span>
						<img src="{{ asset('assets/imgs/hero/hookah.png') }}" alt="" />
					</div>
					<div class="swiper-slide">
						<span class="text" data-swiper-parallax="-200">
							<h2>{{ __("Coctails") }}</h2>
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
                            <h4>
                            Закуски
                            </h4>
                            <h4>
                            Салати
                            </h4>
                            <h4>
                            Гарячі страви
                            </h4>
                            <h4>
                            Бургери
                            </h4>
						</span>
						<img src="{{ asset('assets/imgs/hero/hookah.png') }}" alt="" />
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

	<div class="roomtour">

    </div>

	<div class="menu">

    </div>
</div>
@endsection @push('scripts')

<script
	src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"
	integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA=="
	crossorigin="anonymous" referrerpolicy="no-referrer">
    </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paroller.js/1.4.7/jquery.paroller.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="/assets/js/home.js"></script>
<script src="/assets/js/odessa.js"></script>
@endpush
