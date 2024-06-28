<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Storage: string implements HasLabel  /// Счета
{
    case Place = '0';
    case Owner = '1';
    case Rental = '2';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Place => __('Place'),
            self::Owner => __('Owner'),
            self::Rental => __('Rental')
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Place => 'heroicon-m-home-modern',
            self::Owner => 'heroicon-m-user',
            self::Rental => 'heroicon-m-arrow-left-on-rectangle'
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Place => 'danger',
            self::Owner => 'success',
            self::Rental => 'info'
        };
    }
}

