<?php

namespace App\Livewire;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
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
        ->whereDate('time', '>=', Carbon::today()))

            ->columns([
                TextColumn::make('name')->translateLabel()
                ->description(fn (Booking $record): string => $record->comment ? $record->comment : ' ', position: 'below'),
                TextColumn::make('time')->since()->translateLabel(),
                TextColumn::make('seet.table_id')->translateLabel(),
                TextColumn::make('phone')->translateLabel()
                ->copyable()
                    ->copyMessage(__('Phone copied'))
                    ->copyMessageDuration(1500),
                TextColumn::make('person')->translateLabel(),
            ]);
    }

}
