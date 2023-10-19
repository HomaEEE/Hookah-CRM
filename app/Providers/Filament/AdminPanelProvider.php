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
use App\Livewire\UserProfileData;

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
                    BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                        shouldRegisterNavigation: true, // Adds a main navigation item for the My Profile page (default = false)
                        hasAvatars: false, // Enables the avatar upload form component (default = false)
                        slug: 'my-profile' // Sets the slug for the profile page (default = 'my-profile')
                    )
                    ->myProfileComponents([UserProfileData::class])
                    ->passwordUpdateRules(
                        rules: [Password::default()->mixedCase()->uncompromised(3)], // you may pass an array of validation rules as well. (default = ['min:8'])
                        requiresCurrentPassword: false, // when false, the user can update their password without entering their current password. (default = true)
                        ),
                    //\ChrisReedIO\Socialment\SocialmentPlugin::make(),
                    EnvironmentIndicatorPlugin::make()
                    ,
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
