<?php

namespace App\Filament\Clusters\Settings\Resources\Places\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use App\Filament\Clusters\Settings\Resources\Places\PlaceResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePlace extends CreateRecord
{
    use Translatable;

    protected static string $resource = PlaceResource::class;

    protected function getHeaderActions(): array {}
}
