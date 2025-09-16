<?php

namespace App\Filament\Resources\Supplies\Pages;

use App\Filament\Resources\Supplies\SupplyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSupplies extends ManageRecords
{
    protected static string $resource = SupplyResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
