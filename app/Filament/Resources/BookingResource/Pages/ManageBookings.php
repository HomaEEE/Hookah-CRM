<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
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
            Actions\CreateAction::make()->slideOver(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Booking::query()->whereIn('place_id', Auth::user()->place->pluck('id'));
    }
}
