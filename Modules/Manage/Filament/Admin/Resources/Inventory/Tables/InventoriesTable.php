<?php

namespace Modules\Manage\Filament\Admin\Resources\Inventory\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Modules\Manage\Enums\OwnEnum;
use Modules\Manage\Enums\StateEnum;
use Modules\Manage\Filament\Actions\SalesHistoryAction;
use Modules\Manage\Filament\Actions\SellAction;
use Modules\Manage\Filament\Actions\SettleDebtAction;
use Modules\Manage\Filament\Actions\UpdateOwnBulkAction;
use Modules\Manage\Filament\Actions\UpdateStateBulkAction;
use Modules\Manage\Models\InventoryItem;
use Modules\Manage\Models\InventorySale;

class InventoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['sales', 'warehouse']))
            ->columns([
                TextColumn::make('state')
                    ->label(__('State'))
                    ->badge()
                    ->sortable(),

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
                    ->label(__('Total'))
                    ->money('UAH')
                    ->state(fn ($record) => $record->totalPrice)
                    ->sortable(query: fn ($query, string $direction) => $query->orderByRaw('(quantity * price) '.$direction))
                    ->summarize(
                        Summarizer::make()
                            ->label('Итого активов:')
                            ->money('UAH')
                            ->using(fn ($query) => $query->sum(DB::raw('quantity * price')) / 100)
                    ),

                TextColumn::make('total_sold_quantity')
                    ->label('Продано шт.')
                    ->state(fn (InventoryItem $record): int => $record->total_sold_quantity)
                    ->badge()
                    ->color('success')
                    ->sortable(query: fn ($query, string $direction) => $query->withSum('sales', 'quantity')->orderBy('sales_sum_quantity', $direction)),

                TextColumn::make('sold_total_price')
                    ->label('Выручка')
                    ->money('UAH')
                    ->state(fn (InventoryItem $record) => $record->soldTotalPrice)
                    ->description(function (InventoryItem $record): ?string {
                        $unpaid = $record->total_unpaid_debt;

                        return $unpaid > 0 ? "В долгах: {$unpaid} UAH" : null;
                    })
                    ->sortable(query: fn ($query, string $direction) => $query->withSum('sales as sales_revenue', DB::raw('quantity * price'))->orderBy('sales_revenue', $direction))
                    ->placeholder('—')
                    ->color('success')
                    ->summarize([
                        Summarizer::make()
                            ->label('Реальная выручка:')
                            ->money('UAH')
                            ->using(fn () => ((float) (InventorySale::selectRaw('
                                SUM(CASE WHEN is_debt = 1 THEN paid_amount ELSE (quantity * price) END) as real_sales
                            ')->value('real_sales') ?? 0)) / 100),
                        Summarizer::make()
                            ->label('Сумма долга:')
                            ->money('UAH')
                            ->using(fn () => ((float) (InventorySale::where('is_debt', true)->where('is_debt_settled', false)->sum('debt_amount') ?? 0)) / 100),
                    ]),

            ])
            ->filters([
                SelectFilter::make('own')
                    ->label(__('Owner'))
                    ->native(false)
                    ->options(OwnEnum::class),
                SelectFilter::make('state')
                    ->label(__('State'))
                    ->native(false)
                    ->options(StateEnum::class),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    SellAction::make(),
                    SettleDebtAction::make(),
                    SalesHistoryAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                UpdateOwnBulkAction::make(),
                UpdateStateBulkAction::make(),
            ]);
    }
}
