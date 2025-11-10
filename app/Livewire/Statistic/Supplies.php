<?php

namespace App\Livewire\Statistic;

use App\Models\Supplier;
use App\Models\Supply;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class Supplies extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return __('Supplies');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Supply::query()
            )
            ->columns([
                TextColumn::make('supplier_id')
                    ->label(__('Supplier'))
                    ->sortable(),

                TextColumn::make('count')
                    ->label(__('Count'))
                    ->sortable(),

                TextColumn::make('total')
                    ->label(__('Sum'))
                    ->money('UAH', 100)
                    ->sortable()
                    ->summarize(Sum::make()->money('UAH', 100)->label(__('Total'))),
            ])
            ->filters([
                SelectFilter::make('supplier_id')
                    ->label(__('Supplier'))
                    ->native(false)
                    ->multiple()
                    ->options(Supplier::query()->pluck('company', 'id')),

            ])
            ->hiddenFilterIndicators();
    }
}
