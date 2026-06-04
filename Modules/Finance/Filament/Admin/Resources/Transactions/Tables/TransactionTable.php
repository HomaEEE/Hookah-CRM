<?php

namespace Modules\Finance\Filament\Admin\Resources\Transactions\Tables;

use Carbon\Carbon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Modules\Finance\Models\Transaction;

class TransactionTable
{
    public static function configure(Table $table): Table
    {

        return $table
            ->groups([
                Group::make('added_at')->label(__('Date'))
                    ->getTitleFromRecordUsing(fn ($record) => Carbon::parse($record->added_at)->translatedFormat('d F Y'))
                    ->orderQueryUsing(fn ($query, $direction) => $query->orderByRaw("YEAR(added_at) $direction, MONTH(added_at) $direction")),
                Group::make('bill.type')->label(__('Bill')),
            ])
            ->defaultGroup('added_at')
            ->defaultSort('added_at', direction: 'desc')
            ->columns([

                TextColumn::make('added_at')->label(__('Date'))->dateTime('d-m-Y')
                    ->size('md')
                    ->badge()
                    ->description(fn (Transaction $record): string => $record->user->name ?? ''),

                TextColumn::make('type')->label(__('Type'))->badge(),
                TextColumn::make('category.name')->label(__('Category'))
                    ->badge()
                    ->description(fn (Transaction $record): string => $record->supply->company ?? ''),

                TextColumn::make('sum')->label(__('Sum'))->money('UAH')
                    ->summarize(Sum::make()->money('uah', 100)->label(__('Total'))),

                IconColumn::make('bill.type')->label(__('Bill'))
                    ->action(function ($record) {
                        $record->update([
                            'bill_id' => ! $record->bill_id,
                        ]);
                    }),
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
            ]);
    }
}
