<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Infolist;

class Statistic extends Page
{

    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string $view = 'filament.pages.statistic';


    public static function getPluralModelLabel(): string
    {
        return __('Statistic');
    }

    public static function getNavigationLabel(): string
    {
        return __('Statistic');
    }

    public function getTitle(): string | Htmlable
    {
        return __('Statistic');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Label')
                ->tabs([
                    Tabs\Tab::make('Tab 1')
                        ->schema([
                            TextEntry::make('name'),
                            TextEntry::make('slug'),
                            TextEntry::make('opened'),
                        ]),
                        Tabs\Tab::make('Tab 2')
                        ->schema([
                            TextEntry::make('name'),
                            TextEntry::make('slug'),
                            TextEntry::make('opened'),
                            ])
                        ]),

            ]);
        }


}
