<?php

namespace App\Filament\Clusters\Settings\Resources\PlaceResource\Pages;

use App\Filament\Clusters\Settings\Resources\PlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlace extends EditRecord
{
    protected static string $resource = PlaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
