<?php

namespace App\Filament\Clusters\Settings\Resources\PlaceResource\Pages;

use App\Filament\Clusters\Settings\Resources\PlaceResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePlace extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = PlaceResource::class;

    protected function getHeaderActions(): array {}
}
