<?php

namespace App\Providers\Filament;

use App\Models\Place;
use BezhanSalleh\FilamentLanguageSwitch\FilamentLanguageSwitchPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;

use App\Filament\Pages\Tenancy\EditPlaceProfile;
use Filament\Navigation\NavigationGroup;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Jeffgreco13\FilamentBreezy\BreezyCore;
use Filament\SpatieLaravelTranslatablePlugin;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;

use Filament\Support\Colors\Color;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('web')
            ->colors([
                'primary' => Color::Emerald
            ])
            ->sidebarCollapsibleOnDesktop()
            ->tenant(Place::class, slugAttribute: 'slug', ownershipRelationship: 'place' )
           // ->tenantProfile(EditPlaceProfile::class)
            ->login()
            ->registration()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            // ->navigationGroups([
            //     NavigationGroup::make()
            //     ->label('Dashboard')
            //     ->icon('heroicon-o-shopping-cart'),
            //     NavigationGroup::make()
            //          ->label('Place')
            //          ->icon('heroicon-o-shopping-cart'),
            //     NavigationGroup::make()
            //         ->label('Blog')
            //         ->icon('heroicon-o-pencil'),
            //     NavigationGroup::make()
            //         ->label(fn (): string => __('navigation.settings'))
            //         ->icon('heroicon-o-cog-6-tooth')
            //         ->collapsed(),
            // ])
            ->navigationGroups([
                NavigationGroup::make()
                ->label('Place'),
                NavigationGroup::make()
                ->label('Statistics'),
                NavigationGroup::make()
                ->label('Menu'),
                NavigationGroup::make()
                ->label('Stocks'),
                NavigationGroup::make()
                ->label('Finance'),
                NavigationGroup::make()
                ->label('Manage'),
                NavigationGroup::make()
                ->label('Data'),
                NavigationGroup::make()
                ->label('Settings')
            ])
            ->plugins([
                    FilamentLanguageSwitchPlugin::make(),
                    SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['en', 'ru', 'uk']),
                    \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                    BreezyCore::make(),
                    EnvironmentIndicatorPlugin::make(),
                ])
            ->authGuard('web')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
            //->spa();
    }
}
