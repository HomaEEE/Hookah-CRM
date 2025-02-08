<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Js;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Number;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Log;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

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
                    Log::warning("An individual database query exceeded 1 second.", [
                        'sql' => $query->sql
                    ]);
                }
            });
        }

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['uk','en','ru']);
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
