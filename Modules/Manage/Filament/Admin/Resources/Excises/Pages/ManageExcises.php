<?php

namespace Modules\Manage\Filament\Admin\Resources\Excises\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Modules\Manage\Filament\Admin\Resources\Excises\ExciseResource;

class ManageExcises extends ManageRecords
{
    protected static string $resource = ExciseResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
