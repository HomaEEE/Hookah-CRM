<?php

namespace Modules\Finance\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum BillType: string implements HasColor, HasIcon, HasLabel
{
    case CASH = '1';
    case CARD = '2';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CASH => __('Cash'),
            self::CARD => __('Bank'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::CASH => 'heroicon-m-banknotes',
            self::CARD => 'heroicon-m-credit-card'
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::CASH => 'success',
            self::CARD => 'info'
        };
    }
}
