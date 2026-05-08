<?php

namespace App\Observers;

use Modules\Place\Models\Booking;
use Modules\Place\Models\Guest;

class BookingToGuest
{
    public function created(Booking $booking): void
    {
        Guest::firstOrCreate(
            [
                'name' => $booking->name,
                'phone' => $booking->phone,
                'place_id' => $booking->place_id,
            ]
        );
    }
}
