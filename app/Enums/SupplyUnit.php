<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SupplyUnit: string implements HasLabel, HasColor
{
    case l = 'l';
    case kg = 'kg';
    case p = 'unit';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::l => __('Litr'),
            self::kg => __('Kg'),
            self::p => __('Unit')
        };
    }

    public function getColor(): ?string {
        return match($this) {
            self::l => 'info',
            self::kg => 'warning',
            self::p => 'success'
        };
    }
}
