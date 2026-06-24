<?php

namespace Modules\Manage\Filament\Admin\Resources\Inventory\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Modules\Manage\Filament\Admin\Resources\Inventory\InventoryResource;

class ManageInventory extends ManageRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
