<?php

namespace App\Filament\Clusters\Settings\Resources\Places\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Clusters\Settings\Resources\Places\PlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlaces extends ListRecords
{
    protected static string $resource = PlaceResource::class;

    // use ListRecords\Concerns\Translatable;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
