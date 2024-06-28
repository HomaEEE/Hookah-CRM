<?php

namespace App\Enums;


use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Bill: string implements HasLabel, HasIcon, HasColor
{
   // case BANK = '1';
    case CASH = '1';
    case CARD = '2';

    public function getLabel(): ?string
    {
        return match ($this) {
         //   self::BANK => __('BANK'),
            self::CASH => __('Cash'),
            self::CARD => __('Card'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
          //  self::BANK => 'heroicon-m-building-library',
            self::CASH => 'heroicon-m-banknotes',
            self::CARD => 'heroicon-m-credit-card'
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::BANK => 'info',
            self::CASH => 'warning',
            self::CARD => 'success'
        };
    }
}