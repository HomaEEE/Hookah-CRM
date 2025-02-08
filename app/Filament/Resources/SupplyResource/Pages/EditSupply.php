<?php

namespace App\Filament\Resources\SupplyResource\Pages;

use App\Filament\Components\SupplyForm;
use App\Filament\Resources\ShiftResource;
use App\Models\Supply;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;

class EditSupply extends EditRecord
{
    protected static string $resource = ShiftResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make()
        ];
    }

    // protected function getFormSchema(): array
    // {
    //     SupplyForm::schema()
    // }
}
