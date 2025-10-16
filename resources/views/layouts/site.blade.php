<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  prefix="og: http://ogp.me/ns#">
    <head>
        <meta charset="utf-8">

        <meta name="application-name" content="{{ config('site.name') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no,  user-scalable=no">
        <title>@yield('page') - {{ env('SITE_NAME') }} - {{$place->city}}</title>
        <link rel="preload" href="/assets/fonts/PhonkSans-Light.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="preload" href="/assets/fonts/PhonkSans-LightItalic.woff2" as="font" type="font/woff2" crossorigin>
        <link rel="stylesheet" href="https://unpkg.com/flexboxgrid2@7.2.1/flexboxgrid2.css">
        @vite('resources/css/app.css')
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
        @vite(['resources/js/app.js'])

        @stack('css')

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-EK1VJ7ZBNC"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-EK1VJ7ZBNC');
        </script>

    </head>
<body>
        @include('site.nav')
 <div>
            <main>
                @yield('content')
            </main>
          </div>
        <footer>
            <div class="dev">{{__('Created by')}}
                <a href="https://black-design.pw" rel="noopener noreferrer"><img src="https://black-design.pw/themes/black-design/assets/images/logo-white.svg?v=1.0.1" alt="Black DESIGN"></a>
            </div>
        </footer>

        @stack('scripts')

    </body>
</html>
