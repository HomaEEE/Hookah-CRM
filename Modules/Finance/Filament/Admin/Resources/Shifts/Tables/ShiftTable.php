<?php

namespace Modules\Finance\Filament\Admin\Resources\Shifts\Tables;

use App\Filament\Components\Filters\LastMonthRecordsFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Finance\Models\Shift;

class ShiftTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime('l')->translateLabel()
                    ->size('xs')
                    ->description(fn (Shift $record): string => $record->created_at->format('d-m-Y') ?? ''),

                TextColumn::make('terminal')->translateLabel()->color('success')
                    ->money('UAH')
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total'))),

                TextColumn::make('cash')->translateLabel()->color('warning')
                    ->money('UAH')
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total'))),

                TextColumn::make('sum')->translateLabel()->color('gray')
                    ->state(function (Shift $record): int {
                        return $record->terminal + $record->cash;
                    })->money('UAH'),

                TextColumn::make('shift')->color('info')
                    ->money('UAH')->translateLabel(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                LastMonthRecordsFilter::make('created_at'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()->slideOver(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
