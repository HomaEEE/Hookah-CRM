<?php

namespace App\Filament\Resources\Inventories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InventoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('storage_id')
                    ->numeric(),
                TextEntry::make('name'),
                TextEntry::make('ownership_type')
                    ->badge(),
                TextEntry::make('unit_price')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('quantity')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
