<?php

namespace Modules\Inventory\Filament\Resources\Inventories;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Modules\Inventory\Filament\Resources\Inventories\Pages\EditInventory;
use Modules\Inventory\Filament\Resources\Inventories\Pages\ListInventories;
use Modules\Inventory\Filament\Resources\Inventories\Pages\ViewInventory;
use Modules\Inventory\Filament\Resources\Inventories\Schemas\InventoryForm;
use Modules\Inventory\Filament\Resources\Inventories\Schemas\InventoryInfolist;
use Modules\Inventory\Filament\Resources\Inventories\Tables\InventoriesTable;
use Modules\Inventory\Models\Item;

class InventoryResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getNavigationGroup(): ?string
    {
        return __('Demo');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('Inventory');
    }

    public static function form(Schema $schema): Schema
    {
        return InventoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InventoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InventoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventories::route('/'),
            'view' => ViewInventory::route('/{record}'),
            'edit' => EditInventory::route('/{record}/edit'),
        ];
    }
}
