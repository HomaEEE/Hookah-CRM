<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BillStatus: string implements HasColor, HasLabel
{
    case open = '1';
    case close = '2';
    case deleted = '3';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::open => __('Event'),
            self::close => __('Task'),
            self::deleted => __('Task'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::open => 'info',
            self::close => 'success',
            self::deleted => 'danger
            ',
        };
    }
}
