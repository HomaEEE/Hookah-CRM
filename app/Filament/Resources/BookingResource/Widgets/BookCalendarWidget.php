<?php

namespace App\Filament\BookingResource\Widgets;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class BookCalendarWidget extends FullCalendarWidget
{
    public Model | string | null $model = Booking::class;

    public function fetchEvents(array $fetchInfo): array
    {
        return Booking::where('start', '>=', $fetchInfo['start'])
            ->where('end', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Booking $booking) {
                return [
                    'id'    => $booking->id,
                    'title' => $booking->name,
                    'start' => $booking->time,
                ];
            })
            ->toArray();
    }

    public static function canView(): bool
    {
        return false;
    }
}