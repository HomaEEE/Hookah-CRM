<?php

namespace Modules\Manage\Filament\Admin\Resources\Supplies\Schemas;

use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SupplyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')->schema([
                    TextEntry::make('added_at')->label(__('Date Added'))->dateTime('d F Y')
                        ->badge(),
                    TextEntry::make('user.name')->translateLabel(),
                    TextEntry::make('supplier.company')->label(__('Supplier'))->badge(),
                    TextEntry::make('sum')->label(__('Sum'))->badge()->money('UAH')->copyable(),
                    TextEntry::make('bill.name')->translateLabel()
                        ->badge(),
                    TextEntry::make('invoice_number')->translateLabel()->badge()->copyable(),
                ])->columnSpanFull()->columns(3),
                Section::make('')->schema([
                    TextEntry::make('reqvizite.own')->label(__('FOP'))->badge()->copyable(),
                    TextEntry::make('reqvizite.IBAN')->label(__('IBAN'))->badge()->copyable(),
                    TextEntry::make('reqvizite.INN')->label(__('INN'))->badge()->copyable(),
                ])
                    ->columnSpanFull()->columns(3),

                Tabs::make('Tabs')
                    ->tabs([

                        Tab::make(__('Photo'))
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('invoice')
                                    ->collection('supplies')
                                    ->lightbox()
                                    ->hiddenLabel(),
                                TextEntry::make('comment')->label(__('Comment'))->copyable()->hiddenLabel(),

                            ])->columns(1),
                        Tab::make(__('Supply'))
                            ->schema([
                                RepeatableEntry::make('items')
                                    ->table([
                                        TableColumn::make('Name'),
                                        TableColumn::make('Qty'),
                                        TableColumn::make('Price'),
                                        TableColumn::make('Sum'),
                                    ])
                                    ->schema([
                                        TextEntry::make('ingredient.ingredient')->label('Name'),
                                        TextEntry::make('qty'),
                                        TextEntry::make('price'),
                                        TextEntry::make('sum'),
                                    ]),
                            ]),
                    ])->columnSpanFull()
                    ->contained(false),
            ]);
    }
}
