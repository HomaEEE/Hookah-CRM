<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request):((Response|RedirectResponse))  $next
     * @return Response|RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (Session()->has('applocale') and array_key_exists(Session()->get('applocale'), config('app.languages'))) {
            App::setLocale(Session()->get('applocale'));
            \Carbon\Carbon::setLocale(Session()->get('applocale'));
            \Carbon\CarbonImmutable::setLocale(Session()->get('applocale'));
        } else { // This is optional as Laravel will automatically set the fallback language if there is none specified
            App::setLocale(config('app.locale'));
            \Carbon\Carbon::setLocale(config('app.locale'));
            \Carbon\CarbonImmutable::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
