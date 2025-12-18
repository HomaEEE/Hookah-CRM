<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Storage: string implements HasLabel  // / Счета
{
    case Place = '1';
    case Owner = '2';
    case Rental = '3';
    case Other = '4';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Place => __('Place'),
            self::Owner => __('Owner'),
            self::Rental => __('Rental'),
            self::Other => __('Other')
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Place => 'heroicon-m-home-modern',
            self::Owner => 'heroicon-m-user',
            self::Rental => 'heroicon-m-arrow-left-on-rectangle',
            self::Other => 'heroicon-m-arrow-left-on-rectangle'
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Place => 'danger',
            self::Owner => 'success',
            self::Rental => 'info',
            self::Other => 'grey',
        };
    }
}
