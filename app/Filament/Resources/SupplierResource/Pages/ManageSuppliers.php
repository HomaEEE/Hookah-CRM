<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use Auth;
use App\Filament\Resources\SupplierResource;
use App\Models\Place;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ManageRecords;

class ManageSuppliers extends ManageRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
