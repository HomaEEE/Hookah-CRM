<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
enum MeterType: string implements HasLabel, HasColor
{
    case Water = '1';
    case Gas = '2';
    case Energy = '3';

    public function getLabel(): ?string
    {

        return match ($this) {
            self::Water => '💧',
            self::Gas => '⛽',
            self::Energy => '⚡',
        };
    }

    public function getColor(): ?string {
        return match($this) {
            self::Water => 'info',
            self::Gas => 'warning',
            self::Energy => 'success'
        };
    }
}
