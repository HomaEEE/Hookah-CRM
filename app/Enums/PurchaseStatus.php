<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PurchaseStatus: string implements HasLabel, HasColor
{
    case new = '1';
    case in_process = '2';
    case delivery = '3';
    case complete = '4';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::new => __('New'),
            self::in_process => __('In Process'),
            self::delivery => __('Delivery'),
            self::complete => __('Complete'),
        };
    }

    public function getColor(): ?string {
        return match($this) {
            self::new => 'info',
            self::in_process => 'warning',
            self::delivery => 'warning',
            self::complete => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::new => 'heroicon-m-folder-plus',
            self::in_process => 'heroicon-m-bars-arrow-up',
            self::delivery => 'heroicon-m-archive-box-arrow-down',
            self::complete => 'heroicon-m-check'
        };
    }
}
