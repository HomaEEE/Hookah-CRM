<?php

namespace Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\TelegramBot\Filament\Clusters\Settings\Resources\Bots\BotsResource;

class CreateBots extends CreateRecord
{
    protected static string $resource = BotsResource::class;
}
