<?php

namespace App\Filament\Widgets\Shift;

use App\Models\Supply as Record;
use Auth;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class SuppliesShift extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        return Record::query()->whereIn('place_id', Auth::user()->place->pluck('id'))->whereDate('added_at', Carbon::today());
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
                ->description(fn (Record $record): string => $record->user->name ?? ''),
            TextColumn::make('supplier.company')->label(__('Supplier')),
            TextColumn::make('sum')->label(__('Sum'))->money('uah', true),
            TextColumn::make('type_pay')->label(__('Type pay'))
                ->colors([
                    0 => 'info',
                    1 => 'success',
                ])
                ->enum([
                    0 => __('Cash'),
                    1 => __('Card'),
                ]),
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
