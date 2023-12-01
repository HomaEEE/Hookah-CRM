<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Widgets\Statistic\GraphExpenses;
use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Illuminate\Contracts\Support\Htmlable;

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

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\Statistic\GraphExpenses::make(),
            \App\Filament\Widgets\Statistic\GraphSupply::make(),
            \App\Filament\Widgets\Statistic\GraphShifts::make(),
            \App\Filament\Widgets\Statistic\UserWage::make(),
        ];
    }
}
