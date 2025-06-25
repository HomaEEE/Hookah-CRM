<?php

namespace App\Filament\Clusters\Settings\Resources\BotResource\Pages;

use App\Filament\Clusters\Settings\Resources\BotResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBots extends ManageRecords
{
    protected static string $resource = BotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
