<?php

namespace App\Filament\Clusters\Settings\Resources\Places\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Clusters\Settings\Resources\Places\PlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlace extends EditRecord
{
    protected static string $resource = PlaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
