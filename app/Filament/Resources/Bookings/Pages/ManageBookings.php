<?php

namespace App\Filament\Resources\Bookings\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Booking;
use Auth;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

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
