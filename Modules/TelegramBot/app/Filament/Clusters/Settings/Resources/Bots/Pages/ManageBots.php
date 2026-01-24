<?php

namespace Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\BotResource;

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
