<?php

namespace App\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class UserStatistic extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.pages.user-statistic';

    protected static ?int $navigationSort = 2;

    public static function getPluralModelLabel(): string
    {
        return __('Statistic');
    }

    public static function getNavigationLabel(): string
    {
        return __('Statistic');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Statistic');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\UserStat\UserStatShift::make(),
            \App\Filament\Widgets\UserStat\UserTableShift::make(),
        ];
    }
}
