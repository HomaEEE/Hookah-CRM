<?php

namespace App\Livewire;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;

class EventTable extends BaseWidget
{

    protected function getTableHeading(): string | Htmlable
    {
        return __('Events');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Event::query()->where('place_id', Filament::getTenant()->id)
                ->whereBetween('datetime', [Carbon::now(), Carbon::now()->addDays(7)])
            )
            ->columns([
                TextColumn::make('type')->label(__('Type'))->badge()
                    ->description(fn (Event $record): string => $record->title, position: 'above'),
                TextColumn::make('datetime')->label(__('Day'))->since()
                    ->badge()
                    ->description(fn (Event $record): string => $record->eventTime, position: 'above'),
            ])->defaultSort('datetime', 'asc');
    }
}
