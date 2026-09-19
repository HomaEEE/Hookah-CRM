<?php

namespace Modules\Manage\Filament\Admin\Resources\Inventory\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    TextEntry::make('warehouse.name')->badge()->color('info')->hiddenLabel(true)->size('md'),
                    TextEntry::make('state')->badge()->hiddenLabel(true)->size('md'),
                    TextEntry::make('own')->badge()->hiddenLabel(true)->size('md'),
                ])->columns(3)->columnSpanFull(),
                TextEntry::make('name')->label(__('Name')),
                TextEntry::make('serial_number')->separator(',')->badge()->copyable()->label(__('SN')),
                TextEntry::make('price')->money('UAH')->label(__('price')),
                TextEntry::make('quantity')->numeric()->label(__('qty')),
                TextEntry::make('totalPrice')->money('UAH')->label(__('Total')),
                TextEntry::make('comment')->label(__('Comment')),

                Section::make('История продаж')
                    ->collapsible()
                    ->columnSpanFull()
                    ->schema([
                        RepeatableEntry::make('sales')
                            ->hiddenLabel()
                            ->placeholder('Продажи по этой позиции отсутствуют')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Дата и время')
                                    ->dateTime('d.m.Y H:i'),
                                TextEntry::make('quantity')
                                    ->label('Количество')
                                    ->suffix(' шт.')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('price')
                                    ->label('Цена за шт.')
                                    ->money('UAH'),
                                TextEntry::make('totalPrice')
                                    ->label('Итого')
                                    ->money('UAH')
                                    ->color('success')
                                    ->weight('bold'),
                                TextEntry::make('serial_numbers')
                                    ->label('SN')
                                    ->badge()
                                    ->separator(',')
                                    ->placeholder('—'),
                                TextEntry::make('user.name')
                                    ->label('Сотрудник')
                                    ->placeholder('—'),
                                TextEntry::make('notes')
                                    ->label('Примечание')
                                    ->placeholder('—')
                                    ->columnSpanFull(),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
