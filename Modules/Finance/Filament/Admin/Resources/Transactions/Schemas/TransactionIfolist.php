<?php

namespace Modules\Finance\Filament\Admin\Resources\Transactions\Schemas;

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;

class TransactionIfolist
{
    public static function getInfoList(): array
    {
        return [
            TextEntry::make('added_at')->translateLabel()->badge()->dateTime('d F Y'),
            TextEntry::make('user.name')->translateLabel()->badge(),
            TextEntry::make('bill.name')->translateLabel()->badge(),
            TextEntry::make('category.name')->translateLabel()->badge(),
            TextEntry::make('sum')->translateLabel()->badge(),
            TextEntry::make('comment')->translateLabel()->badge(),
            SpatieMediaLibraryImageEntry::make('invoice')
                ->collection('supplies')
                ->lightbox()
                ->hiddenLabel(),
        ];
    }
}
