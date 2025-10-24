<?php

namespace App\Filament\Clusters\Statistic\Pages;

use App\Filament\Clusters\Statistic\StatisticCluster;
use App\Filament\Widgets\UserStat\UserStatShift;
use App\Filament\Widgets\UserStat\UserTableShift;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class UserStatistic extends Page
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'phosphor-user';

    protected static ?string $cluster = StatisticCluster::class;

    protected string $view = 'filament.pages.user-statistic';

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
            UserStatShift::make(),
            UserTableShift::make(),
        ];
    }
}
