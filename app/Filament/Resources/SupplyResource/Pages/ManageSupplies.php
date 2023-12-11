<?php

namespace App\Filament\Resources\SupplyResource\Pages;

use App\Filament\Resources\SupplyResource;
use App\Models\Place;
use App\Models\Supply;
use Auth;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords\Tab;
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
}
