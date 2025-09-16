<?php

namespace App\Filament\Clusters\Data\Resources\Services\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Clusters\Data\Resources\Services\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageServices extends ManageRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->createAnother(false),
        ];
    }
}
