<?php

namespace Modules\Manage\Filament\Admin\Resources\Supplies\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Modules\Manage\Filament\Admin\Resources\Supplies\SupplyResource;

class ManageSupplies extends ManageRecords
{
    protected static string $resource = SupplyResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
