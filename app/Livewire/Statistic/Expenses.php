<?php

namespace App\Livewire\Statistic;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;

class Expenses extends BaseWidget
{
    // protected int|string|array $columnSpan = 'full';

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
                    ->multiple()
                    ->options(
                        ExpenseCategory::query()
                            ->pluck('name', 'id')
                            ->toArray()
                    ),
            ])

            ->modifyQueryUsing(function ($query) {
                $startDate = ! is_null($this->pageFilters['startDate'] ?? null) ?
                    Carbon::parse($this->pageFilters['startDate']) :
                    null;

                $endDate = ! is_null($this->pageFilters['endDate'] ?? null) ?
                    Carbon::parse($this->pageFilters['endDate']) :
                    now();

                // Применение фильтрации по датам, если значения присутствуют
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at');
                }

                $query->selectRaw('category_id as id, category_id, sum(sum) as total')
                    ->groupBy('category_id')
                    ->orderBy('sum', 'desc');
            });
    }
}
