<?php

namespace App\Filament\Clusters\Settings\Resources\Places\Pages;

use Filament\Actions\EditAction;
use App\Filament\Clusters\Settings\Resources\Places\PlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlace extends ViewRecord
{
    protected static string $resource = PlaceResource::class;

    protected function getActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
