<?php

namespace App\Filament\Resources\SupplyResource\Pages;

use App\Enums\SupplierType;
use App\Filament\Resources\SupplyResource;
use App\Models\{Supply, Supplier, Workshop};
use Auth;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ManageSupplies extends ManageRecords
{
    protected static string $resource = SupplyResource::class;


    // protected function getTableQuery(): Builder
    // {
    //     return Supply::query()->whereIn('place_id', Auth::user()->place->pluck('id'));
    // }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sum'] = $data['sum'] * 100;

        return $data;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['sum'] = $data['sum'] / 100;

        return $data;
    }

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    // public function getTabs(): array
    // {
    //     $tabs = [null => Tab::make(__('All'))];

    //     $types = SupplierType::class ;

    //     dd($types);

    //     foreach ($types as $type) {
    //         $tabs[__($type->value)] = Tab::make()->query(function ($query) use ($type) {
    //             $query->where('type', $type->key)
    //             ;
    //         });
    //     }

    //     return $tabs;
    // }
}
