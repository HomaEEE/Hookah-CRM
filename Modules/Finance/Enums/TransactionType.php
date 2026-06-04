<?php

namespace Modules\Finance\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use ToneGabes\Filament\Icons\Enums\Phosphor;

enum TransactionType: string implements HasColor, HasIcon, HasLabel
{
    case Expense = '1';
    case Income = '2';
    case Transfer = '3';
    case Withdrawal = '4';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Income => __('Income'),
            self::Expense => __('Expense'),
            self::Transfer => __('Transfer'),
            self::Withdrawal => __('Withdrawal'),
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Income => Phosphor::CaretUp,
            self::Expense => Phosphor::CaretDown,
            self::Transfer => Phosphor::ArrowsLeftRight,
            self::Withdrawal => Phosphor::Wallet
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Income => 'success',
            self::Expense => 'danger',
            self::Transfer => 'info',
            self::Withdrawal => 'warning'
        };
    }
}
