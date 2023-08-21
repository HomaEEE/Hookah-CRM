<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Js;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Model::unguard();

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // FilamentAsset::register([
        //     Js::make('admin-sw', '/resources/js/admin-sw.js'),
        // ]);
        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): View => view('filament.meta.meta'),
        );

        Http::macro('posterApi', function () {
            return Http::baseUrl('https://joinposter.com/api');
        });
    }


}
