<?php

namespace App\Filament\Clusters\Data;

use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;

class DataCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'data';

    public static function getNavigationLabel(): string
    {
        return __('Data');
    }
}
