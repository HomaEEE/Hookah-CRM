<?php

namespace App\Filament\Resources\Excises\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Excises\ExciseResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageExcises extends ManageRecords
{
    protected static string $resource = ExciseResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
