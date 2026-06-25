<?php

namespace Modules\Manage\Filament\Admin\Resources\Inventory\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InventoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('serial_number'),
                TextEntry::make('comment'),
                TextEntry::make('price'),
                TextEntry::make('quantity'),
                TextEntry::make('total'),
                TextEntry::make('warehouse.name'),
            ]);
    }
}
