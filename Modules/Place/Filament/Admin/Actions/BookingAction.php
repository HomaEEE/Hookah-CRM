<?php

namespace Modules\Place\Filament\Admin\Actions;

use Filament\Actions\CreateAction;
use Modules\Place\Filament\Admin\Resources\Booking\Schemas\BookingForm;
use Modules\Place\Models\Booking;

class BookingAction
{
    public static function create(): CreateAction
    {
        return CreateAction::make()
            ->model(Booking::class)
            ->createAnother(false)
            ->modalWidth('sm')
            ->form(BookingForm::configure(...));
    }
}
