<?php

namespace Modules\Finance\Filament\Admin\Resources\Transactions\Schemas;

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('added_at')->translateLabel()->dateTime('d F Y'),
                TextEntry::make('type')->translateLabel(),
                TextEntry::make('user.name')->translateLabel(),
                TextEntry::make('bill.name')->translateLabel()->badge(),
                TextEntry::make('category.name')->label(__('Category'))->badge(),
                TextEntry::make('sum')->translateLabel()->badge()->copyable(fn ($record) => $record->added_at),
                TextEntry::make('comment')->translateLabel()->copyable(),
                SpatieMediaLibraryImageEntry::make('invoice')
                    ->collection('supplies')
                    ->lightbox()
                    ->hiddenLabel(),
            ])->columns(3);
    }
}
