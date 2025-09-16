<?php

namespace App\Filament\Resources\Leftovers\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Leftovers\LeftoverResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeftover extends EditRecord
{
    protected static string $resource = LeftoverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
