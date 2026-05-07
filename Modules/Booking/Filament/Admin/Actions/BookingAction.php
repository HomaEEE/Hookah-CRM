<?php

namespace Modules\Booking\Filament\Admin\Actions;

use Filament\Actions\CreateAction;
use Modules\Booking\Filament\Admin\Resources\Booking\Schemas\BookingForm;
use Modules\Booking\Models\Booking;

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
