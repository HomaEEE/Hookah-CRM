<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Priority: string implements HasLabel  /// Счета
{
    case Low = '0';
    case Middle = '1';
    case High = '2';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Low => __('Low'),
            self::Middle => __('Middle'),
            self::High => __('High')
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Low => 'heroicon-o-chevron-up',
            self::Middle => 'heroicon-o-chevron-double-up',
            self::High => 'heroicon-o-fire'
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Low => 'info',
            self::Middle => 'success',
            self::High => 'danger'
        };
    }
}

