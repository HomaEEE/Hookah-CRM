<?php

namespace App\Filament\Clusters\Settings\PlaceResource\Pages;

use App\Filament\Clusters\Settings\PlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlace extends ViewRecord
{
    protected static string $resource = PlaceResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
