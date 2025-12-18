<?php

namespace App\Filament\Resources\Inventories\Pages;

use App\Enums\Storage;
use App\Filament\Resources\Inventories\InventoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageInventories extends ManageRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            __('all') => Tab::make(),
        ];

        foreach (Storage::cases() as $storage) {
            $tabs[$storage->getLabel()] = Tab::make()
                ->icon($storage->getIcon())
                ->modifyQueryUsing(
                    fn (Builder $query) => $query->where('ownership_type', $storage->value)
                );
        }

        return $tabs;
    }
}
