<?php

namespace Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\BotsResource;

class ListBots extends ListRecords
{
    protected static string $resource = BotsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
