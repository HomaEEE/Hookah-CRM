<?php

namespace App\Filament\Resources\ArrearResource\Pages;

use App\Filament\Resources\ArrearResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageArrears extends ManageRecords
{
    protected static string $resource = ArrearResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
