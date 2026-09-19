<link rel="manifest" href="{{ url('/manifest-admin.json') }}" />
<meta name="mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="application-name" content="{{ config('app.name') }}" />
<meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}" />
<meta name="msapplication-starturl" content="{{ url('/admin') }}" />
<link rel="apple-touch-icon" href="{{ asset('assets/imgs/pwa/favicon-touch.png') }}" />
<link rel="icon" type="image/png" href="{{ asset('assets/imgs/pwa/favicon-crm.png') }}" />
<link rel="icon" type="image/svg+xml" href="{{ asset('assets/imgs/pwa/favicon-crm.svg') }}" />