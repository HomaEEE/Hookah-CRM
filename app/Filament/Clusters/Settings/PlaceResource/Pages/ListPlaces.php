<?php

namespace App\Filament\Clusters\Settings\PlaceResource\Pages;

use App\Filament\Clusters\Settings\PlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlaces extends ListRecords
{
    protected static string $resource = PlaceResource::class;

    use ListRecords\Concerns\Translatable;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
            Actions\CreateAction::make(),
        ];
    }
}
