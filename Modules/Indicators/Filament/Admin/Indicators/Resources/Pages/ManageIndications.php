<?php

namespace Modules\Indicators\Filament\Admin\Indicators\Resources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Modules\Indicators\Filament\Admin\Indicators\Resources\IndicationResource;

class ManageIndications extends ManageRecords
{
    protected static string $resource = IndicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
