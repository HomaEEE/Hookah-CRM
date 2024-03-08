<?php

namespace App\Livewire;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use GuzzleHttp\Promise\Create;
use Illuminate\Contracts\Support\Htmlable;

class Bookings extends BaseWidget
{

    protected function getTableHeading(): string | Htmlable
    {
        return __('Bookings');
    }

    public function table(Table $table): Table
    {
        return $table
        ->query(Booking::query()->where('place_id', Filament::getTenant()->id)
        ->whereBetween('time', [Carbon::now(), Carbon::now()->addDays(7)]))

            ->columns([
                TextColumn::make('guest.name')->label(__('Name'))
                ->description(fn (Booking $record): string => $record->guest->comment ? $record->guest->comment : $record->guest->phone, position: 'below'),
                TextColumn::make('time')->since()->translateLabel()
                ->description(fn (Booking $record): string => $record->bookingTime, position: 'below'),
                TextColumn::make('table')->translateLabel()->badge(),
                TextColumn::make('guest.phone')->label(__('Phone'))
                ->copyable()
                    ->copyMessage(__('Phone copied'))
                    ->copyMessageDuration(1500),
                TextColumn::make('person')->translateLabel(),
            ])->defaultSort('time', 'asc')
            ->actions([
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
                })
            ])
            ->headerActions([
                CreateAction::make()
            ]);
    }

}
