<?php

namespace App\Filament\Resources\Shifts\Pages;

use App\Filament\Resources\Shifts\ShiftResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewShift extends ViewRecord
{
    protected static string $resource = ShiftResource::class;

    protected static ?string $recordTitleAttribute = 'title';

    public function getTitle(): string
    {

        return __('Shift').' '.$this->record->created_at->translatedFormat('d F Y');
    }

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
