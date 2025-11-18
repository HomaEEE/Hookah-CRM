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
                Select::make('storage_id')
                    ->relationship('storage', 'name')
                    ->required(),
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
