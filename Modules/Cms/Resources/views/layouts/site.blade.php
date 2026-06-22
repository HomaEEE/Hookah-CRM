
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  prefix="og: http://ogp.me/ns#">
    <head>
        <meta charset="utf-8">

        <meta name="application-name" content="{{ config('site.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no,  user-scalable=no">
        <title>{{ config('app.site') }} - @yield('page') - {{$place->city}}</title>
        <meta name="description" content="@yield('description', __('They don\'t chase the air here for nothing'))">

        <meta name="keywords" content="@yield('keywords', __('They don\'t chase the air here for nothing'))">

        <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@200..900&display=swap" rel="stylesheet">
        @vite(['resources/js/app.js', 'resources/css/frontend/app.css'])

        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="application-name" content="{{ config('site.name') }}" />
        <meta name="apple-mobile-web-app-title" content="{{ config('site.name') }}" />
        <meta name="msapplication-starturl" content="/" />

        <link rel="icon" href="{{ asset('assets/imgs/site/favicon.ico') }}" sizes="32x32">
        <link rel="icon" href="{{ asset('assets/imgs/site/favicon.svg') }}" type="image/svg+xml" sizes="any">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/imgs/site/favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('assets/imgs/site/favicon-touch.png') }}" sizes="180x180">

        <meta property="og:title" content="{{ config('site.name') }} - @yield('page')"/>
        <meta property="og:description" content="@yield('description', 'They don\'t chase the air here for nothing')"/>
        <meta property="og:site_name" content="{{ config('site.name') }}"/>

        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ url()->current() }}/" />
        <meta property="og:image" content="{{ asset ('assets/imgs/site/1200-600.jpg') }}"/>
        <meta property="og:image:width" content="1200"/>
        <meta property="og:image:height" content="600"/>
        <link rel="canonical" href="{{ url()->current() }}">

         @stack('schema')

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-NRZVZTD');</script>
        <!-- End Google Tag Manager -->
    </head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NRZVZTD"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
        <div class="w-full min-h-screen">
            @include('cms::site.partials.nav')

            <main>
                @yield('content')
            </main>

        </div>

        <footer>
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="md:text-left  w-full md:flex-1"> {{__('All rights reserved')}}</div>
                    <div class="md:text-center w-full md:flex-1">{{ __('Smoking is harmful to your health') }}</div>
                    <div class="md:text-right w-full md:flex-1 ">
                        {{__('Created by')}}
                        <a href="https://black-design.pw" target="_blank"><img src="https://black-design.pw/assets/img/logo-white.svg?v=1.0.1" alt="Black DESIGN"></a>
                    </div>
                </div>
            </div>
        </footer>

        @stack('scripts')
    </div>
    </body>
</html>
