<?php

namespace App\Filament\Clusters\Statistic;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class StatisticCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 3;

    public static function getPluralModelLabel(): string
    {
        return __('Statistic');
    }

    public static function getNavigationLabel(): string
    {
        return __('Statistic');
    }
}
