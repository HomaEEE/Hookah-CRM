<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditUserProfile;
use App\Filament\Pages\Tenancy\EditPlaceProfile;
use App\Filament\Pages\Tenancy\RegisterPlace;
use App\Http\Middleware\ApplyTenantScopes;
use App\Http\Middleware\SetLocale;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;
use SolutionForest\FilamentSimpleLightBox\SimpleLightBoxPlugin;
use SolutionForest\FilamentTranslateField\FilamentTranslateFieldPlugin;

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
                'primary' => Color::Slate,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->tenant(\App\Models\Place::class, slugAttribute: 'slug')
            ->tenantRegistration(RegisterPlace::class)
            ->tenantProfile(EditPlaceProfile::class)
            ->login()
            // ->registration()
            ->passwordReset()
            ->profile(EditUserProfile::class)
            ->databaseNotifications()
            ->databaseNotificationsPolling('120s')
            ->unsavedChangesAlerts()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')

            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')

            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])

            ->navigationGroups([

                NavigationGroup::make()
                    ->label(__('Menu'))
                    ->icon('heroicon-o-qr-code'),

                NavigationGroup::make()
                    ->label(__('Manage'))
                    ->icon('heroicon-o-arrow-down-on-square-stack'),

                NavigationGroup::make()
                    ->label(__('Finance'))
                    ->icon('heroicon-o-banknotes'),
            ])

            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 2,
                        'sm' => 1,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
                FilamentApexChartsPlugin::make(),
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['ru', 'uk', 'en']),
                FilamentTranslateFieldPlugin::make()
                    ->defaultLocales(['ru', 'uk', 'en']),
                EnvironmentIndicatorPlugin::make(),
                SimpleLightBoxPlugin::make(),
            ])
            ->tenantMiddleware([
                SyncShieldTenant::class,
                ApplyTenantScopes::class,
            ], isPersistent: true)
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
                SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,

            ]);
    }
}
