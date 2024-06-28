<?php

namespace App\Observers;

use App\Models\{Booking, Guest};

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
