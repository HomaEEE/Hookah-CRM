<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Enums\SupplierType;
use App\Filament\Resources\SupplierResource;
use App\Models\Supplier;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;

use function PHPSTORM_META\type;

class ManageSuppliers extends ManageRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->createAnother(false),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [null => Tab::make(__('All'))];

        $types = SupplierType::cases();
        foreach ($types as $type) {
            $tabs[__($type->name)] = Tab::make()->query(function ($query) use ($type) {
                $query->where('type', $type->value);
            });
        }

        return $tabs;
    }
}
