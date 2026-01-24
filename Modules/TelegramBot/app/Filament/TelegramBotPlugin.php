<?php

namespace Modules\TelegramBot\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class TelegramBotPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'TelegramBot';
    }

    public function getId(): string
    {
        return 'telegrambot';
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
