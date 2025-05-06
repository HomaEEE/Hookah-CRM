<?php

namespace App\Filament\Clusters\Data\Resources\GuestResource\Pages;

use App\Filament\Clusters\Data\Resources\GuestResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGuests extends ManageRecords
{
    protected static string $resource = GuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->createAnother(false),
        ];
    }
}
