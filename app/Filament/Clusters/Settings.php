<?php

namespace App\Filament\Clusters;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Clusters\Cluster;

class Settings extends Cluster
{
    use HasPageShield;

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
