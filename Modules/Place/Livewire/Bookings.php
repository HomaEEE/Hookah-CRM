<?php

namespace Modules\Place\Livewire;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Modules\Place\Filament\Admin\Resources\Booking\Schemas\BookingForm;
use Modules\Place\Models\Booking;

class Bookings extends BaseWidget
{
    protected function getTableHeading(): string|Htmlable
    {
        return __('Bookings');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Booking::query()->where('place_id', Filament::getTenant()->id)
                ->whereBetween('time', [Carbon::now(), Carbon::now()->addDays(7)]))

            ->columns([
                TextColumn::make('guest.phone')->label(__('Name'))
                    ->copyable()
                    ->copyMessage(__('Phone copied'))
                    ->copyMessageDuration(1500)
                    ->description(fn (Booking $record): string => $record->guest->fullName ? $record->guest->fullName : $record->guest->name, position: 'above'),
                TextColumn::make('time')->since()->translateLabel()->badge()
                    ->description(fn (Booking $record): string => $record->bookingTime, position: 'above'),
                TextColumn::make('seet.number')->translateLabel()
                    ->badge()->separator(','),
                TextColumn::make('person')->translateLabel(),
            ])->defaultSort('time', 'asc')
            ->recordActions([
                Action::make('complete')->hiddenLabel()
                    ->button()
                    ->icon('heroicon-o-arrow-down')
                    ->visible(fn (Booking $record) => $record['complete'] == 0)
                    ->action(function (Booking $record): void {

                        $record->update(
                            [
                                'complete' => true,
                            ]
                        );
                        $record->save();
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->model(Booking::class)
                    ->createAnother(false)
                    ->modalWidth('sm')
                    ->form(BookingForm::configure(...)),
            ]);
    }
}
