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

use App\Livewire\PersonalInfo;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Illuminate\Validation\Rules\Password;

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
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
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
            ->tenant(Place::class, slugAttribute: 'slug', ownershipRelationship: 'place' )
        //    // ->tenantProfile(EditPlaceProfile::class)
            ->login()
            ->registration()
            ->passwordReset()
            ->profile()
            ->databaseNotifications()
            ->databaseNotificationsPolling('10s')
            ->unsavedChangesAlerts()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
                    SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['ru', 'uk', 'en']),
                    FilamentTranslateFieldPlugin::make()
                    ->defaultLocales(['ru', 'uk', 'en']),
                    BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                        shouldRegisterNavigation: false, // Adds a main navigation item for the My Profile page (default = false)
                        hasAvatars: false, // Enables the avatar upload form component (default = false)
                        slug: 'my-profile' // Sets the slug for the profile page (default = 'my-profile')
                    )
                    ->myProfileComponents([
                        'personal_info' => PersonalInfo::class,
                    ])
                    ->passwordUpdateRules(
                        rules: [Password::default()->mixedCase()->uncompromised(3)], // you may pass an array of validation rules as well. (default = ['min:8'])
                        requiresCurrentPassword: false, // when false, the user can update their password without entering their current password. (default = true)
                        ),
                    EnvironmentIndicatorPlugin::make(),
                    SimpleLightBoxPlugin::make(),
                    FilamentShieldPlugin::make(),
                    FilamentApexChartsPlugin::make(),
                    FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable()
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
    }
}
