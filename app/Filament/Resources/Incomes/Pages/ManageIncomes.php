<?php

namespace App\Filament\Resources\Incomes\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Incomes\IncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

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
