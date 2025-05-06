<?php

namespace App\Filament\Clusters\Data\Resources\ServiceResource\Pages;

use App\Filament\Clusters\Data\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageServices extends ManageRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->createAnother(false),
        ];
    }
}
