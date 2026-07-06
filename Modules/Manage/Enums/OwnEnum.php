<?php

namespace Modules\Manage\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum OwnEnum: string implements HasColor, HasIcon, HasLabel
{
    case Own = '1';
    case Rent = '2';
    case Premises = '3';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Own => __('Own'),
            self::Rent => __('Rent'),
            self::Premises => __('Premises'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Own => 'heroicon-m-building-office',
            self::Rent => 'heroicon-m-building-office-2',
            self::Premises => 'heroicon-m-building-office-2',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Own => 'success',
            self::Rent => 'danger',
            self::Premises => 'info',
        };
    }
}
