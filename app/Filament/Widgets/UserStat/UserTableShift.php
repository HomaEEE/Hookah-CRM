<?php

namespace Modules\Finance\Filament\Admin\Widgets\UserStat;

use App\Models\User;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Modules\Finance\Models\ShiftUser;

class UserTableShift extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string|Htmlable
    {
        return __('Shifts');
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ShiftUser::query()
            )
            ->defaultPaginationPageOption(10)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label(__('Date Added'))->dateTime('d/m/Y')->badge(),
                TextColumn::make('days')->label(__('Days'))->badge(),
                TextColumn::make('procent_day')->translateLabel()
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total procents'))),
                TextColumn::make('wage')->translateLabel()
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total wages'))),
                TextColumn::make('cashout')->translateLabel()
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total'))),
            ])
            ->filters([
                SelectFilter::make('user_id')->options(
                    User::get()->pluck('name', 'id'),
                ),
                DateRangeFilter::make('created_at')->translateLabel()
                    ->defaultLast30Days(),
            ]);

    }
}
