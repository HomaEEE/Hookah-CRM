<?php

namespace App\Filament\Resources\Inventories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InventoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('storage_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                Select::make('ownership_type')
                    ->options(['own' => 'Own', 'rented' => 'Rented', 'other' => 'Other'])
                    ->required(),
                TextInput::make('unit_price')
                    ->numeric()
                    ->default(null),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
