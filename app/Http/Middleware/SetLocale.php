<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $availableLocales = config('app.languages', []);
        $defaultLocale = config('app.locale', 'uk');

        $requestedLocale = $request->route('lang') ?? $request->get('lang');

        if (
            $requestedLocale &&
            array_key_exists($requestedLocale, $availableLocales)
        ) {
            session(['applocale' => $requestedLocale]);
        }

        $locale = session('applocale', $defaultLocale);

        // защита от мусора в session
        if (! array_key_exists($locale, $availableLocales)) {
            $locale = $defaultLocale;
            session(['applocale' => $locale]);
        }

        App::setLocale($locale);

        Carbon::setLocale($locale);
        CarbonImmutable::setLocale($locale);

        setlocale(LC_TIME, $locale);
        setlocale(LC_MONETARY, $locale);

        return $next($request);
    }
}
