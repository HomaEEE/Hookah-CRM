<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SupplierType: string implements HasLabel, HasColor
{
    case Products = '0';
    case Alcohol = '1';
    case Tabacco = '2';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Products => __('Products'),
            self::Alcohol => __('Alcohol'),
            self::Tabacco => __('Tabacco')
        };
    }

    public function getColor(): ?string {
        return match($this) {
            self::Products => 'info',
            self::Alcohol => 'warning',
            self::Tabacco => 'success'
        };
    }
}
