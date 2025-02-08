<?php

namespace App\Filament\Resources\SupplyResource\Pages;

use App\Filament\Resources\Actions\NextAction;
use App\Filament\Resources\Actions\PreviousAction;
use App\Filament\Resources\Pages\Concerns\CanPaginateViewRecord;
use App\Filament\Resources\SupplyResource;
use App\Models\Supply;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSupply extends ViewRecord
{

    use CanPaginateViewRecord;

    protected static string $resource = SupplyResource::class;

    protected function getActions(): array
    {
        return [
            EditAction::make(),
            PreviousAction::make(),
            NextAction::make(),

        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('Supply');
    }



}
