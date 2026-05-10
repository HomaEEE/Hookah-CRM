<?php

namespace Modules\Finance\Filament\Admin\Clusters\Statistic;

use BackedEnum;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;

class StatisticCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Phosphor::ChartBar;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getPluralModelLabel(): string
    {
        return __('Statistic');
    }

    public static function getNavigationLabel(): string
    {
        return __('Statistic');
    }
}
