<?php

namespace App\Filament\Resources\DocResource\Pages;

use App\Filament\Resources\DocResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDocs extends ManageRecords
{
    protected static string $resource = DocResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->slideOver(),
        ];
    }
}
