<?php

namespace Modules\Finance\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ExpenseType: string implements HasColor, HasIcon, HasLabel
{
    case Supply = 'Supply';
    case Expense = 'Expense';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Supply => __('Supply'),
            self::Expense => __('Expense'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Supply => 'heroicon-m-queue-list',
            self::Expense => 'heroicon-m-minus'
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Supply => 'info',
            self::Expense => 'danger'
        };
    }
}
