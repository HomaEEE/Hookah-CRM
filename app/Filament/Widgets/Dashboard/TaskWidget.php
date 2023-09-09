<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Task;
use Auth;
use Carbon\Carbon;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class TaskWidget extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';


    protected function getTableHeading(): string | Htmlable
    {
        return __('Tasks');
    }

    protected function getTableQuery(): Builder
    {
        return Task::query()->whereIn('place_id', Auth::user()->place->pluck('id'));
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
            TextColumn::make('name')->label(__('Task')),
            TextColumn::make('forUser.name')->label(__('For User')),
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
