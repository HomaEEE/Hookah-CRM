<?php

namespace App\Filament\Resources\ExciseResource\Pages;

use App\Filament\Resources\ExciseResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageExcises extends ManageRecords
{
    protected static string $resource = ExciseResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
