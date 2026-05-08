<?php

namespace Modules\Place\Filament\Admin\Resources\Booking\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Modules\Place\Filament\Admin\Resources\Booking\BookingResource;

class ManageBookings extends ManageRecords
{
    protected static string $resource = BookingResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make()->createAnother(false),
        ];
    }
}
