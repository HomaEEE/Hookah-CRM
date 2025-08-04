<?php

namespace App\Filament\Clusters\Settings\Resources\PlaceResource\Pages;

use App\Filament\Clusters\Settings\Resources\PlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlaces extends ListRecords
{
    protected static string $resource = PlaceResource::class;

    // use ListRecords\Concerns\Translatable;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
