<?php

namespace App\Filament\Clusters\Settings;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;

class SettingsCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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
