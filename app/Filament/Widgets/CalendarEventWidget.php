<?php

namespace App\Filament\Widgets;


use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarEventWidget extends FullCalendarWidget
{
    public Model | string | null $model = Event::class;

    public function fetchEvents(array $fetchInfo): array
    {
        return Event::where('datetime', '>=', $fetchInfo['start'])
            ->where('datetime', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Event $task) {
                return [
                    'id'    => $task->id,
                    'title' => $task->title,
                    'start' => $task->datetime,
                    'end'   => $task->datetime,
                ];
            })
            ->toArray();
    }

    public static function canView(): bool
    {
        return false;
    }
}