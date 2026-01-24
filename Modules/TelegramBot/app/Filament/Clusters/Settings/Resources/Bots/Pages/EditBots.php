<?php

namespace Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\BotsResource;

class EditBots extends EditRecord
{
    protected static string $resource = BotsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
