<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Booking;
use Auth;
use Carbon\Carbon;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class BookingWidget extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';


    protected function getTableHeading(): string | Htmlable
    {
        return __('Bookings');
    }

    protected function getTableQuery(): Builder
    {
        return Booking::query()
        ->whereDate('time', '>=', Carbon::today());
    }

    public function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 10;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'time';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'asc';
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')->translateLabel()
            ->description(fn (Booking $record): string => $record->comment ? $record->comment : ' ', position: 'below'),
            TextColumn::make('time')->since()->translateLabel(),
            TextColumn::make('seet.table_id')->translateLabel()->toggleable(),
            TextColumn::make('phone')->translateLabel()
            ->copyable()
                ->copyMessage(__('Phone copied'))
                ->copyMessageDuration(1500),
            TextColumn::make('person')->translateLabel()->toggleable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [

        ];
    }
}
