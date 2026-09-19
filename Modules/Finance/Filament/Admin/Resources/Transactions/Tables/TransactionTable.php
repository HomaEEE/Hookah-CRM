<?php

namespace Modules\Finance\Filament\Admin\Resources\Transactions\Tables;

use App\Filament\Components\Filters\LastMonthRecordsFilter;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Models\Transaction;

class TransactionTable
{
    public static function configure(Table $table): Table
    {

        return $table
            ->defaultSort('added_at', direction: 'desc')
            ->filters([
                LastMonthRecordsFilter::make('created_at'),
                SelectFilter::make('expense_category_id')
                    ->label('Категория')
                    ->native(false)
                    ->options(
                        ExpenseCategory::query()
                            ->pluck('name', 'id')
                            ->toArray()
                    ),

            ])
            ->columns([
                TextColumn::make('added_at')->label(__('Date'))->dateTime('d-m-Y')
                    ->size('md')
                    ->badge()
                    ->description(fn (Transaction $record): string => $record->user->name ?? ''),

                TextColumn::make('type')->label(__('Type'))->badge()
                    ->description(fn (Transaction $record): string => $record->comment ?? ''),
                TextColumn::make('category.name')->label(__('Category'))
                    ->badge()
                    ->description(fn (Transaction $record): string => $record->supply?->supplier?->company ?? ''),

                TextColumn::make('sum')->label(__('Sum'))->money('UAH')
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total'))),

                IconColumn::make('bill_id')
                    ->label(__('Bill'))
                    ->boolean(),
                IconColumn::make('poster')
                    ->translateLabel()
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-x-mark')
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => ! $record->$name,
                        ]);
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Actions\ViewAction::make(),
                    Actions\EditAction::make(),
                    Actions\DeleteAction::make(),
                ]),
            ]);
    }
}
