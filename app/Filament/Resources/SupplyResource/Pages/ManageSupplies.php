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
use Illuminate\Contracts\Pagination\Paginator;

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
