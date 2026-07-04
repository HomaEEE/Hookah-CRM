<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditUserProfile;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\Tenancy\EditPlaceProfile;
use App\Filament\Pages\Tenancy\RegisterPlace;
use App\Http\Middleware\ApplyTenantScopes;
use App\Http\Middleware\SetLocale;
use App\Models\Place;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;
use Bjanczak\FilamentFlexFields\FilamentFlexFieldsPlugin;
use Daikazu\FilamentLightbox\LightBoxPlugin;
use Filafly\Icons\Phosphor\PhosphorIcons;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\View\View;
use JibayMcs\Tabbed\TabbedPlugin;
use MarcelWeidum\Passkeys\PasskeysPlugin;
use Openplain\FilamentShadcnTheme\Color;

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
                'primary' => Color::Default,  // The Shadcn effect
            ])

            ->viteTheme('resources/css/filament/admin/theme.css')
            ->sidebarCollapsibleOnDesktop()
            ->tenant(Place::class, slugAttribute: 'slug')
            ->tenantRegistration(RegisterPlace::class)
            ->tenantProfile(EditPlaceProfile::class)
            ->login()
            ->maxContentWidth(Width::Full)
            // ->registration()
            ->passwordReset()
            ->profile(EditUserProfile::class)
            // ->topbar(false)
            ->globalSearch(false)
            ->databaseNotifications()
            ->databaseNotificationsPolling('120s')
            ->unsavedChangesAlerts()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')

            ->pages([
                Dashboard::class,
            ])

            ->renderHook(
                PanelsRenderHook::USER_MENU_PROFILE_AFTER,
                fn (): View => view('filament.hooks.lang-switcher'),
            )

            ->plugins([
                PasskeysPlugin::make(),
                FilamentShieldPlugin::make()
                    ->localizePermissionLabels()
                    ->scopeToTenant(true),
                PhosphorIcons::make()->regular(),
                LightBoxPlugin::make(),
                TabbedPlugin::make()
                    ->defaultPage('view')
                    ->confirmClose()
                    ->lazyLoad(),
                FilamentFlexFieldsPlugin::make(),
            ])
            ->tenantMiddleware([
                SyncShieldTenant::class,
                SetLocale::class,
                //   ApplyTenantScopes::class,
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
