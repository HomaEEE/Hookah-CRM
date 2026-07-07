<?php

namespace Modules\Finance\Filament\Admin\Resources\Transactions\Schemas;

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Transaction')->label(__('Transaction'))
                            ->schema([
                                TextEntry::make('added_at')->translateLabel()->dateTime('d F Y')->size('md'),
                                TextEntry::make('type')->translateLabel()->size('md'),
                                TextEntry::make('user.name')->translateLabel()->size('md'),
                                TextEntry::make('bill_id')->label(__('Bill'))->badge()->size('lg'),
                                TextEntry::make('category.name')->label(__('Category'))->badge()->size('xl'),
                                TextEntry::make('sum')->translateLabel()->color('success')->copyable(fn ($record) => $record->added_at)->size('md'),
                                TextEntry::make('comment')->translateLabel()->copyable()->size('lg'),
                            ])->columns(2),
                        Tab::make(__('Photo'))
                            ->schema([
                                SpatieMediaLibraryImageEntry::make('invoice')
                                    ->collection('expenses')
                                    ->lightbox()
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ]);
    }
}
