<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Currency: string implements HasLabel  /// Счета
{
    case UAH = 'UAH';
    case USD = 'USD';
    case EUR = 'EUR';

    public function getLabel(): ?string
    {
        return $this->value;
   }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::UAH => 'warning',
            self::USD => 'success',
            self::EUR => 'info'
        };
    }
}
