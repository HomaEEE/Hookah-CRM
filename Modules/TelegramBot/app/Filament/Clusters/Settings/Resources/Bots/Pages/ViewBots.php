<?php

namespace Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\BotsResource;

class ViewBots extends ViewRecord
{
    protected static string $resource = BotsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
