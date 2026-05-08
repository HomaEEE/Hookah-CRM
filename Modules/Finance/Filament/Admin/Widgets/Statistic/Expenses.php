<?php

namespace Modules\Finance\Filament\Admin\Widgets\Statistic;

use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Malzariey\FilamentDaterangepickerFilter\Enums\OpenDirection;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Modules\Finance\Models\Expense;
use Modules\Finance\Models\ExpenseCategory;

class Expenses extends TableWidget
{
    use InteractsWithPageFilters;

    protected function getTableHeading(): string
    {
        return __('Expenses');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Expense::query()
            )
            ->columns([

                TextColumn::make('category.name')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('count')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('total')
                    ->label(__('Sum'))
                    ->sortable()
                    ->money('UAH', 100)
                    ->summarize(Sum::make()->money('UAH', 100)->label(__('Total'))),
            ])

            ->filters([
                SelectFilter::make('category_id')
                    ->label('Категория')
                    ->native(false)
                    ->options(
                        ExpenseCategory::query()
                            ->pluck('name', 'id')
                            ->toArray()
                    ),
                DateRangeFilter::make('created_at')
                    ->translateLabel()
                    ->defaultThisMonth()
                    ->showDropdowns()->opens(OpenDirection::LEFT),

            ])
            ->modifyQueryUsing(function ($query) {
                $query->selectRaw('category_id as id, category_id, sum(sum) as total, count(*) as count')
                    ->groupBy('category_id')
                    ->orderBy('sum', 'desc');
            });
    }
}
