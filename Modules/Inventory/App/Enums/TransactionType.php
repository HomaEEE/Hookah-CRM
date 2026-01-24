<?php

namespace Modules\Inventory\App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TransactionType: string implements HasColor, HasLabel
{
    case income = '1';
    case expense = '2';
    case movement = '3';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::income => __('income'),
            self::expense => __('expense'),
            self::movement => __('movement'),
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::income => 'info',
            self::expense => 'danger',
            self::movement => 'grey'

        };
    }
}
