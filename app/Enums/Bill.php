<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BILL: string implements HasLabel  /// Счета
{
    case CASH = '1';
    case CARD = '2';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CASH => __('Cash'),
            self::CARD => __('CARD'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::CASH => 'heroicon-m-banknotes',
            self::CARD => 'heroicon-m-credit-card'
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::CASH => 'warning',
            self::CARD => 'success'
        };
    }
}