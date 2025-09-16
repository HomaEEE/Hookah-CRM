<?php

namespace App\Filament\Clusters\Settings\Resources\Bots\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Clusters\Settings\Resources\Bots\BotResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBots extends ManageRecords
{
    protected static string $resource = BotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
