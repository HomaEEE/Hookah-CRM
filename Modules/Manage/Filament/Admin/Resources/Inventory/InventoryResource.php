<?php

namespace Modules\Manage\Filament\Admin\Resources\Inventory;

use App\Enums\NavigationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Manage\Filament\Admin\Resources\Inventory\Pages\ManageInventory;
use Modules\Manage\Filament\Admin\Resources\Inventory\Schemas\InventoryForm;
use Modules\Manage\Filament\Admin\Resources\Inventory\Schemas\InventoryInfolist;
use Modules\Manage\Filament\Admin\Resources\Inventory\Tables\InventoriesTable;
use Modules\Manage\Filament\Admin\Resources\Inventory\Widgets\InventoryStatsWidget;
use Modules\Manage\Models\InventoryItem;
use UnitEnum;

class InventoryResource extends Resource
{
    protected static ?string $model = InventoryItem::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Manage;

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

    public static function table(Table $table): Table
    {
        return InventoriesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InventoryInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageInventory::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            InventoryStatsWidget::class,
        ];
    }
}
