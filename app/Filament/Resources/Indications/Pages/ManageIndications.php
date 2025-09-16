<?php

namespace App\Filament\Resources\Indications\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Indications\IndicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageIndications extends ManageRecords
{
    protected static string $resource = IndicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
