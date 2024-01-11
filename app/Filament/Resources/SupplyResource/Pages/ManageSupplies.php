<?php

namespace App\Filament\Resources\SupplyResource\Pages;

use App\Filament\Resources\SupplyResource;
use App\Models\{Supply, Supplier};
use Auth;
use Filament\Actions\CreateAction;
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
    //     return [
    //         null => Tab::make('All')->label(__('All')),
    //          __('Products') => Tab::make()->query(fn ($query) => $query->whereHas('supplier', function ($query) {
    //             $query->where('supplier.type', 1);
    //     })
    // ),
    //         // __('Alcohol') => Tab::make()->query(fn ($query) => $query->where('status', 'processing')),
    //         // __('Tabacco') => Tab::make()->query(fn ($query) => $query->where('status', 'shipped')),
    //     ];
    // }
}
