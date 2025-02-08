<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CalendarType: string implements HasLabel, HasColor
{
    case event = 'event';
    case task = 'task';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::event => __('Event'),
            self::task => __('Task'),
        };
    }

    public function getColor(): ?string {
        return match($this) {
            self::event => 'info',
            self::task => 'success',
        };
    }
}
