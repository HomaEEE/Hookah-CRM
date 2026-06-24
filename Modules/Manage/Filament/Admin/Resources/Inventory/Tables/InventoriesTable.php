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

class InventoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(_('Name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('serial_number')
                    ->label(_('SN'))
                    ->searchable()
                    ->badge()
                    ->separator(',')
                    ->limitList(10)
                    ->expandableLimitedList()
                    ->copyable(),

                TextColumn::make('warehouse.name')
                    ->label(_('Storage'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Кол-во')
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Цена за шт.')
                    ->money('UAH') // Форматирование в гривны
                    ->sortable(),

                TextColumn::make('total') // <-- Изменили системное имя!
                    ->label('Общая сумма')
                    ->money('UAH')

    // 1. Указываем, откуда брать данные для отображения (из Аксессора модели)
                    ->state(fn ($record) => $record->totalPrice)

    // 2. Учим сортировать через сырой SQL
                    ->sortable(query: fn ($query, string $direction) => $query->orderByRaw('(quantity * price_per_unit) '.$direction)
                    )

    // 3. Кастомный подсчет суммы в подвале таблицы
                    ->summarize(
                        Summarizer::make()
                            ->label('Итого активов:')
                            ->money('UAH')
                            ->using(fn ($query) => $query->sum(DB::raw('quantity * price')) / 100)
                    ),

                TextColumn::make('comment')
                    ->label('Комментарий')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('warehouse_id')
                    ->label(_('Storage'))
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
