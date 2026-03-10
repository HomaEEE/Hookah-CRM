
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  prefix="og: http://ogp.me/ns#">
    <head>
        <meta charset="utf-8">

        <meta name="application-name" content="{{ config('site.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no,  user-scalable=no">
        <title>{{ config('site') }} - @yield('page') - {{$place->city}}</title>
        <link rel="preload" href="/assets/fonts/PhonkSans-Light.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/assets/fonts/PhonkSans-LightItalic.woff2" as="font" type="font/woff2" crossorigin>
        @vite(['resources/js/app.js' , 'resources/css/app.css'])
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

        <meta name="description" content="@yield('description', 'Описание по умолчанию')">

        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="application-name" content="{{ config('site.name') }}" />
        <meta name="apple-mobile-web-app-title" content="Noire Smoke Different" />
        <meta name="msapplication-starturl" content="/" />

        <meta property="og:title" content="Noire Smoke Different"/>
        <meta property="og:description" content=""/>
        <meta property="og:site_name" content="{{ config('site.name') }}"/>

        <meta property="og:type" content="page" />
        <meta property="og:url" content="{{ url()->current() }}/" />
        <meta property="og:image" content="https://noire.ee/assets/imgs/pwa/1200x600.jpg"/>
        <meta property="og:image:width" content="1200"/>
        <meta property="og:image:height" content="600"/>
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
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NRZVZTD"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
        <div class="w-full min-h-screen">
            @include('site.partials.nav')

            <main>
                @yield('content')
            </main>

        </div>

         <footer class="mt-12">
            <div class="block dev">{{__('Created by')}}
                <a href="https://black-design.pw" rel="noopener noreferrer"><img src="https://black-design.pw/assets/img/logo-white.svg?v=1.0.1" alt="Black DESIGN"></a>
            </div>
        </footer>

        @stack('scripts')
    </div>
    </body>
</html>
