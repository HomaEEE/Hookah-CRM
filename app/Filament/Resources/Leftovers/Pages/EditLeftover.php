<?php

namespace App\Filament\Resources\LeftoverResource\Pages;

use App\Filament\Resources\LeftoverResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLeftover extends EditRecord
{
    protected static string $resource = LeftoverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
