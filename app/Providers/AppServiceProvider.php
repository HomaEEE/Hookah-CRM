<?php

namespace App\Providers;

use AbdulmajeedJamaan\FilamentTranslatableTabs\TranslatableTabs;
use App\Models\Permission;
use App\Models\Role;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        app(\Spatie\Permission\PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        Model::unguard();

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        if (app()->environment('local')) {
            DB::whenQueryingForLongerThan(500, function (Connection $connection) {
                Log::warning("Database queries exceeded 5 seconds on {$connection->getName()}");
            });

            DB::listen(function ($query) {
                if ($query->time > 1000) {
                    Log::warning('An individual database query exceeded 1 second.', [
                        'sql' => $query->sql,
                    ]);
                }
            });
        }

        TranslatableTabs::configureUsing(function (TranslatableTabs $component) {
            $component
                // locales labels
                ->localesLabels([
                    'ru' => __('RU'),
                    'uk' => __('UA'),
                    'en' => __('EN'),
                ])
                // default locales
                ->locales(['ru', 'uk', 'en']);
        });

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['uk', 'en', 'ru']);
        });

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
