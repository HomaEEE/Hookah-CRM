<?php

namespace App\Filament\Clusters\Dev;

use Filament\Clusters\Cluster;

class DevCluster extends Cluster
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-bug-ant';

    protected static ?int $navigationSort = 99;

    public static function getPluralModelLabel(): string
    {
        return __('Dev');
    }

    public static function getNavigationLabel(): string
    {
        return __('Dev');
    }
}
