<?php

namespace Modules\Finance\Filament\Admin\Resources\Incomes\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Modules\Finance\Filament\Admin\Resources\Incomes\IncomeResource;

class ManageIncomes extends ManageRecords
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
