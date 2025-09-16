<?php

namespace App\Filament\Resources\Shifts\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Shifts\ShiftResource;
use App\Models\Shift;
use Filament\Resources\Pages\ViewRecord;

class ViewShift extends ViewRecord
{
    protected static string $resource = ShiftResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('Shift');
    }

}
