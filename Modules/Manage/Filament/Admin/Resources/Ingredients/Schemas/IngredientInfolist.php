<?php

namespace Modules\Manage\Filament\Admin\Resources\Ingredients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class IngredientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('ingredient')->translateLabel(),
                TextEntry::make('barcode')->translateLabel()->badge()->copyable(),
                TextEntry::make('unit')->translateLabel()->badge(),
                TextEntry::make('price')->translateLabel()->badge()->money('UAH', 100),
            ]);
    }
}
