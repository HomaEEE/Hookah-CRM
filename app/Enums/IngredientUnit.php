<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum IngredientUnit: string implements HasLabel // , HasColor
{
    case l = 'l';
    case kg = 'kg';
    case unit = 'p';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::l => __('Liter'),
            self::kg => __('Kg'),
            self::unit => __('Unit'),
        };
    }

    // public function getColor(): ?string
    // {
    //     return match ($this) {
    //         self::l => 'info',
    //         self::kg => 'warning',
    //         self::unit => 'success'
    //     };
    // }
}
