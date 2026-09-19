<?php

namespace Modules\Manage\Filament\Admin\Resources\Inventory\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Modules\Manage\Filament\Admin\Resources\Inventory\InventoryResource;
use Modules\Manage\Filament\Admin\Resources\Inventory\Widgets\InventoryStatsWidget;
use Modules\Manage\Models\Warehouse;

class ManageInventory extends ManageRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InventoryStatsWidget::class,
        ];
    }

    public function getTabs(): array
    {
        $tabs = [null => Tab::make(__('All'))];

        $warehouses = Warehouse::all();
        foreach ($warehouses as $warehouse) {
            $tabs[__($warehouse->name)] = Tab::make()->query(function ($query) use ($warehouse) {
                $query->where('warehouse_id', $warehouse->id);
            });
        }

        return $tabs;
    }
}
