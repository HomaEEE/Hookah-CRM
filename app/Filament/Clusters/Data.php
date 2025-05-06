<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Data extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'data';

    public static function getNavigationLabel(): string
    {
        return __('Data');
    }
}
