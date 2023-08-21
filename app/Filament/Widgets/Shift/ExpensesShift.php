<?php

namespace App\Filament\Widgets\Shift;

use App\Enums\Bill as BillEnum;
use App\Models\Expense as Record;
use Auth;
use Carbon\Carbon;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ExpensesShift extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';

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
                TextColumn::make('place.city')->label(__('City')),
                TextColumn::make('category.name')->label(__('Category')),
                TextColumn::make('sum')->label(__('Sum'))->money('uah', true),
                TextColumn::make('type_pay')->translateLabel()
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

    public function getTabs(): array
    {

        $tabs = [null => Tab::make(__('All'))];

        $places = Auth::user()->place;

        foreach ($places as $place) {
            $tabs[$place->city] = Tab::make()->query(function ($query) use ($place) {
                $query->where('place_id', $place->id);
            });
        }

        return $tabs;
    }
}
