<?php

namespace Modules\Dev\Filament\Admin\Clusters\Dev;

use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;

class DevCluster extends Cluster
{
    protected static ?int $navigationSort = 99;

    protected static string|\BackedEnum|null $navigationIcon = Phosphor::Bug;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getPluralModelLabel(): string
    {
        return __('Dev');
    }

    public static function getNavigationLabel(): string
    {
        return __('Dev');
    }
}
