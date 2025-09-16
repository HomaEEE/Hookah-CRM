<?php

namespace App\Filament\Resources\Arrears\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Arrears\ArrearResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageArrears extends ManageRecords
{
    protected static string $resource = ArrearResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
