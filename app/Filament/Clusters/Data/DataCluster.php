<?php

namespace App\Filament\Clusters\Data;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Clusters\Cluster;

class DataCluster extends Cluster
{
    use HasPageShield;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'data';

    public static function getNavigationLabel(): string
    {
        return __('Data');
    }
}
