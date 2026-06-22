<div class="hero relative  md:p-16">
	<div class="bg">
		<img src="{{ asset('assets/imgs/hero/bg.webp') }}" alt="">
	</div>
    <div class="container mx-auto px-4">
		<div class="hero-title gradient-title text-reveal" >{{ __('Home Hero title') }}
		</div>

	<div class="bento-ui flex flex-col md:grid md:grid-cols-3 grid-rows-2 md:grid-rows-3 gap-4">
		<div class="flex-1 md:col-span-2 row-span-2 md:row-span-3 bento title-box">
			<p class="whitespace-pre-line">{{ __('Bento title') }}</p>
			<span ><a href="#reserve">{{ __('Reserve table') }} <div class="arrow"></div></a></span>
		</div>
		<div class="flex-1 row-span-2 bento menu-box">
			<a href="/menu">
				<span>{{ __('Menu') }} <div class="arrow"></div></span>
			 </a>
		</div>
		<div class="flex-1 row-span-2 bento rating-box flex items-center">
			<p class="flex-1">4.5<sup>/5</sup></p>
			<span class="flex-1">{{ __('Google feedbacks') }}</span>
		</div>
	</div>
</div>
</div>
