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
                // Получаем фильтры с правильными ключами
                $from = $this->pageFilters['from'] ?? null;
                $to = $this->pageFilters['to'] ?? null;

                // Применяем фильтр по датам
                if ($from) {
                    $query->whereDate('created_at', '>=', Carbon::parse($this->pageFilters['from'] ?? null));
                }
                if ($to) {
                    $query->whereDate('created_at', '<=', Carbon::parse($this->pageFilters['to'] ?? null));
                }

                // Группировка: считаем количество и сумму
                return $query
                    ->selectRaw('
                        category_id as id,
                        category_id,
                        COUNT(*) as count,
                        SUM(sum) as total
                    ')
                    ->groupBy('category_id')
                    ->orderByDesc('total');
            });
    }
}
