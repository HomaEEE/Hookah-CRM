<?php

namespace Modules\Manage\Filament\Admin\Resources\Inventory\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Modules\Manage\Models\InventoryItem;

class InventoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->description(fn (InventoryItem $record): string => $record->comment ?? '')
                    ->wrap()
                    ->sortable(),

                TextColumn::make('serial_number')
                    ->label(__('SN'))
                    ->searchable()
                    ->badge()
                    ->separator(',')
                    ->limitList(10)
                    ->expandableLimitedList()
                    ->copyable(),

                TextColumn::make('warehouse.name')
                    ->label(__('Storage'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label(__('qty'))
                    ->sortable(),

                TextColumn::make('price')
                    ->label(__('price'))
                    ->money('UAH')
                    ->sortable(),

                TextColumn::make('total')
                    ->label(__('total'))
                    ->money('UAH')
                    ->state(fn ($record) => $record->totalPrice)
                    ->sortable(query: fn ($query, string $direction) => $query->orderByRaw('(quantity * price_per_unit) '.$direction)
                    )
                    ->summarize(
                        Summarizer::make()
                            ->label('Итого активов:')
                            ->money('UAH')
                            ->using(fn ($query) => $query->sum(DB::raw('quantity * price')) / 100)
                    ),

            ])
            ->filters([
                SelectFilter::make('warehouse_id')
                    ->label(__('storage'))
                    ->relationship('warehouse', 'name'),
            ])
            ->actions([

                EditAction::make(),
                DeleteAction::make(),

            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
