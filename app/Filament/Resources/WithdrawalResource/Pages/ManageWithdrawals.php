<?php

namespace App\Filament\Resources\WithdrawalResource\Pages;

use App\Filament\Resources\WithdrawalResource;
use Auth;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWithdrawals extends ManageRecords
{
    protected static string $resource = WithdrawalResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make()->modalWidth('xs')->createAnother(false),
        ];
    }

}
