<?php

namespace Modules\Arrears\Filament\Admin\Arrears\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Modules\Arrears\Filament\Admin\Arrears\Resources\ArrearResource;

class ManageArrears extends ManageRecords
{
    protected static string $resource = ArrearResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
