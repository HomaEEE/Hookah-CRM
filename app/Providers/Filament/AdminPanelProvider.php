<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Tenancy\EditPlaceProfile;
use App\Filament\Pages\Tenancy\RegisterPlace;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use App\Models\Place;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;

use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\SpatieLaravelTranslatablePlugin;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;

use Filament\Support\Colors\Color;
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
                'primary' => Color::Emerald
            ])
            ->sidebarCollapsibleOnDesktop()
            ->tenant(model: Place::class,  slugAttribute: 'slug')
            ->tenantRegistration(RegisterPlace::class)
            ->tenantProfile(EditPlaceProfile::class)
            ->login()
            ->registration()
            ->passwordReset()
            ->profile(EditProfile::class)
            ->databaseNotifications()
            ->databaseNotificationsPolling('120s')
            ->unsavedChangesAlerts()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])

            ->navigationGroups([

                NavigationGroup::make()
                ->label(__('Place'))
                ->icon('heroicon-o-home-modern'),

                NavigationGroup::make()
                ->label(__('Menu'))
                ->icon('heroicon-o-qr-code'),

                NavigationGroup::make()
                ->label(__('Manage'))
                ->icon('heroicon-o-arrow-down-on-square-stack'),

                NavigationGroup::make()
                ->label(__('Finance'))
                ->icon('heroicon-o-banknotes'),

                NavigationGroup::make()
                ->label('Data')
                ->icon('heroicon-o-arrow-down-on-square-stack'),

                NavigationGroup::make()
                ->label(__('Links'))
                ->icon('heroicon-o-link'),

                NavigationGroup::make()
                ->label(__('Settings'))
                ->icon('heroicon-o-home-modern'),
            ])

            ->navigationItems([
                NavigationItem::make('Poster')
                    ->url('https://noireod.joinposter.com', shouldOpenInNewTab: true)
                    ->group(__('Links'))
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                ->sectionColumnSpan(1)
                ->checkboxListColumns([
                    'default' => 1,
                    'sm' => 2,
                    'lg' => 4,
                ])
                ->resourceCheckboxListColumns([
                    'default' => 2,
                    'sm' => 2,
                ])
                    ->centralApp(Place::class),

                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['ru', 'uk', 'en']),
                FilamentTranslateFieldPlugin::make()
                    ->defaultLocales(['ru', 'uk', 'en']),
                EnvironmentIndicatorPlugin::make(),
                SimpleLightBoxPlugin::make(),
            ])
            ->tenantMiddleware([
                SyncShieldTenant::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
