<?php

namespace Modules\Indicators\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum MeterType: string implements HasColor, HasLabel
{
    case WATER = '1';
    case GAS = '2';
    case ENERGY = '3';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::WATER => __('Water'),
            self::GAS => __('Gas'),
            self::ENERGY => __('Energy')
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::WATER => 'info',
            self::GAS => 'warning',
            self::ENERGY => 'success'
        };
    }
}
