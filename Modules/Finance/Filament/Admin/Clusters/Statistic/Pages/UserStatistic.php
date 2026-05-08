<?php

namespace Modules\Finance\Filament\Admin\Clusters\Statistic\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Finance\Filament\Admin\Clusters\Statistic\StatisticCluster;
use Modules\Finance\Filament\Admin\Widgets\UserStat\UserStatShift;
use Modules\Finance\Filament\Admin\Widgets\UserStat\UserTableShift;

class UserStatistic extends Page
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'phosphor-user';

    protected static ?string $cluster = StatisticCluster::class;

    protected string $view = 'finance::pages.user-statistic';

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
