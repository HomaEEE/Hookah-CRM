<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Dev extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';

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
