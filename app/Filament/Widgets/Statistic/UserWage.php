<?php

namespace App\Filament\Widgets\Statistic;

use Auth;

use App\Models\ShiftUser;
use Carbon\Carbon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Illuminate\Database\Eloquent\Builder;

class UserWage extends BaseWidget
{

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string | Htmlable
    {
        return __('User Wage');
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
                ShiftUser::query()->where('user_id', Auth::user()->id)
            )->defaultPaginationPageOption(30)
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label(__('Date Added'))->dateTime('d/m/Y')->badge(),
                TextColumn::make('procent_day')->translateLabel()
                ->summarize(Sum::make()->money('uah', 100)->label(__('Total procents'))),
                TextColumn::make('wage')->translateLabel()
                ->summarize(Sum::make()->money('uah', 100)->label(__('Total wages'))),
                TextColumn::make('cashout')->translateLabel()
                ->summarize(Sum::make()->money('uah', 100)->label(__('Total')))
            ])
            ->filters([
                DateRangeFilter::make('created_at')->translateLabel()->separator(' - ')
                ->defaultLast30Days()
            ]);
    }
}
