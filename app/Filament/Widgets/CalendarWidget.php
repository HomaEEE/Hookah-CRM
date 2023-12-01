<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{

    protected function headerActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalHeading('Dodaj zadanie'),
        ];
    }

    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function viewAction(): Action
    {
        return ViewAction::make();
    }

    public function getFormSchema(): array
    {
        return BookingResource::getFullFormSchema();
    }

    public function fetchEvents(array $fetchInfo): array
    {
        $tasks = Booking::query()
            ->where('time', '>=', $fetchInfo['start'])
            ->where('time', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (Booking $event) => EventData::make()
                    ->id($event->id)
                    ->title($event->name)
                    ->start($event->planned_at)
                    ->end($event->planned_at)
                    ->url(
                        url: BookingResource::getUrl(name: 'view', parameters: ['record' => $event]),
                        shouldOpenUrlInNewTab: true
                    )
            )
            ->all();

        // dd($tasks);

        return $tasks;
    }
}