<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EventType: string implements HasLabel, HasColor
{
    case Event = '0';
    case DJ = '1';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Event => __('Event'),
            self::DJ => __('DJ'),
        };
    }

    public function getColor(): ?string {
        return match($this) {
            self::Event => 'info',
            self::DJ => 'success',
        };
    }
}
