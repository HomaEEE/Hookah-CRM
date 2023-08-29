<?php

namespace App\Filament\Widgets\Shift;

use App\Enums\Bill as BillEnum;
use App\Models\Expense;
use Auth;
use Carbon\Carbon;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ExpensesShift extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';


    protected function getTableHeading(): string | Htmlable
    {
        return __('Expenses of Shift');
    }

    protected function getTableQuery(): Builder
    {
        return Expense::query()->whereIn('place_id', Auth::user()->place->pluck('id'))
        ->whereDate('added_at', Carbon::today())->with('category', 'user', 'supply');
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
                ->description(fn (Expense $record): string => $record->user->name ?? ''),
                TextColumn::make('place.city')->label(__('City')),
                TextColumn::make('category.name')->label(__('Category'))->badge()
                ->description(fn (Expense $record): string => $record->supply->supplier->company ?? ''),
                TextColumn::make('sum')->label(__('Sum'))->money('uah', true),
                TextColumn::make('type_pay')->translateLabel()->badge()
                ->formatStateUsing(
                 fn (string $state): string => match ($state) {
                     '0' => __('Cash'),
                     '1' => __('Card'),
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
