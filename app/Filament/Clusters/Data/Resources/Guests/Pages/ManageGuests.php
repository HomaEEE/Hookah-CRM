<?php

namespace App\Filament\Clusters\Data\Resources\Guests\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Clusters\Data\Resources\Guests\GuestResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGuests extends ManageRecords
{
    protected static string $resource = GuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->createAnother(false),
        ];
    }
}
