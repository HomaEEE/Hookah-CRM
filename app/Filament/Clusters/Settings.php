<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?int $navigationSort = 5;

    public static function getPluralModelLabel(): string
    {
        return __('Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('Settings');
    }
}
