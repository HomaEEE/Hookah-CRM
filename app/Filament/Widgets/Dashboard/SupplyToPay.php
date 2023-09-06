<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Supply;
use Auth;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class SupplyToPay extends BaseWidget

{

    protected int | string | array $columnSpan = 'full';


    protected function getTableHeading(): string | Htmlable
    {
        return __('Supplies to pay');
    }

    protected function getTableQuery(): Builder
    {
        return Supply::query()->where('place_id', Filament::getTenant()->id)
        ->where('pay', 0);
    }

    public function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 10;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('added_at')->label(__('Date Added'))->dateTime('d/m/Y')
                ->size('sm')
                ->description(fn (Supply $record): string => $record->user->name ?? ''),
                TextColumn::make('category.name')->label(__('Category'))->badge()
                ->description(fn (Supply $record): string => $record->supplier->company ?? ''),
                TextColumn::make('sum')->label(__('Sum'))->money('uah', true),
                TextColumn::make('type_pay')->translateLabel()->badge()
                ->formatStateUsing(
                 fn (string $state): string => match ($state) {
                     '0' => __('Cash'),
                     '1' => __('Card'),
                 }),
                 IconColumn::make('pay')
                    ->translateLabel()
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => ! $record->$name,
                        ]);
                    }),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('place_id')->label(__('Place'))
                ->searchable()
                ->options(
                    Auth::user()->place->pluck('nameCity', 'id')
                ),
        ];
    }
}
