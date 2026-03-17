{{-- resources/views/errors/404.blade.php --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  prefix="og: http://ogp.me/ns#">
    <head>
        <meta charset="utf-8">

        <meta name="application-name" content="{{ config('site.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no,  user-scalable=no">
        <title>{{ config('app.site') }} - {{ __('Page not found') }}</title>

        <link rel="preload" href="/assets/fonts/PhonkSans-Light.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/assets/fonts/PhonkSans-LightItalic.woff2" as="font" type="font/woff2" crossorigin>
        @vite(['resources/js/app.js' , 'resources/css/app.css'])
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

        <link rel="canonical" href="{{ url()->current() }}">

        @stack('css')

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-NRZVZTD');</script>
        <!-- End Google Tag Manager -->
    </head>
    <body class="flex flex-col min-h-screen items-center relative justify-center">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NRZVZTD"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

<main class="flex items-center justify-center my-auto bg-gray-100">
        <div class="error-page">
            <h1 class="title">{{ __('Page not found') }}</h1>

            <div class="error-code">
                <span class="digit">4</span>
                <div class="bowl-container">
                    <img src="{{ asset('assets/imgs/404/bowl.png') }}" alt="0" class="static-bowl" id="staticBowl">
                    <div class="slideshow-mask-wrapper" id="slideshowWrapper">
                        <img src="{{ asset('assets/imgs/404/slides/1.jpg') }}" class="slide active" alt="Slide 1">
                        <img src="{{ asset('assets/imgs/404/slides/2.jpg') }}" class="slide" alt="Slide 2">
                        <img src="{{ asset('assets/imgs/404/slides/3.jpg') }}" class="slide" alt="Slide 3">
                        <img src="{{ asset('assets/imgs/404/slides/4.jpg') }}" class="slide" alt="Slide 4">
                        <img src="{{ asset('assets/imgs/404/slides/5.jpg') }}" class="slide" alt="Slide 4">
                    </div>
                </div>
                <span class="digit">4</span>
            </div>

            <div class="buttons-container">
                <a href="{{ route('home') }}" class="btn btn-outline">
                    {{ __('Back to home') }} <span class="arrow">→</span>
                </a>
                 <a href="{{ route('menu') }}" class="btn btn-outline">
                    {{ __('Menu') }} <span class="arrow">→</span>
                </a>
            </div>

            </main>
            <footer class="mt-12">
            <div class="block dev">{{__('Created by')}}
                <a href="https://black-design.pw" target="_blank"><img src="https://black-design.pw/assets/img/logo-white.svg" alt="Black DESIGN"></a>
            </div>
        </footer>



        @stack('scripts')
    </div>

    <script>
window.addEventListener("load", () => {
    const staticBowl = document.getElementById('staticBowl');
    const slideshowWrapper = document.getElementById('slideshowWrapper');
    const slides = document.querySelectorAll('.slide');
    if (slides.length <= 1) return;

    let currentSlideIndex = 0;
    let slideshowInterval;

    setTimeout(() => {
        staticBowl.style.opacity = '0';
        slideshowWrapper.style.opacity = '1';
        slideshowInterval = setInterval(() => {
            slides[currentSlideIndex].classList.remove('active');
            currentSlideIndex = (currentSlideIndex + 1) % slides.length;
            slides[currentSlideIndex].classList.add('active');
        }, 300);

    }, 1300);
});
    </script>
    </body>
</html>

