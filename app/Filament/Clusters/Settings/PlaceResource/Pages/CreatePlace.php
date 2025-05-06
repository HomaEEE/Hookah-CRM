<?php

namespace App\Filament\Clusters\Settings\PlaceResource\Pages;

use App\Filament\Clusters\Settings\PlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePlace extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = PlaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
