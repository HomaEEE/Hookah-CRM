<?php

namespace App\Livewire\Statistic;

use App\Models\Supplier;
use App\Models\Supply;
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
