<?php

namespace App\Livewire\Statistic;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Malzariey\FilamentDaterangepickerFilter\Enums\OpenDirection;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class Expenses extends BaseWidget
{
    // protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return __('Expenses');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Expense::query()->where('place_id', Filament::getTenant()->id)
            )
            ->columns([

                Tables\Columns\TextColumn::make('category.name')
                    ->translateLabel()
                    ->sortable(),
                Tables\Columns\TextColumn::make('count')
                    ->translateLabel()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
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
                    ->separator(' - ')
                    ->defaultThisMonth()
                    ->showDropdowns()->opens(OpenDirection::LEFT),

            ])
            // ->hiddenFilterIndicators()
            ->modifyQueryUsing(function ($query) {

                $query->selectRaw('category_id as id, category_id, sum(sum) as total, count(*) as count')
                    ->groupBy('category_id')
                    ->orderBy('sum', 'desc');
            });
    }
}
