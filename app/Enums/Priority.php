<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Priority: string implements HasColor, HasIcon, HasLabel
{
    case Low = 'low';
    case Middle = 'middle';
    case High = 'high';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Low => __('Low'),
            self::Middle => __('Middle'),
            self::High => __('High')
        };
    }

    public function getTitle(): string
    {
        return match ($this) {
            self::Low => __('Low'),
            self::Middle => __('Middle'),
            self::High => __('High')
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Low => 'heroicon-o-chevron-up',
            self::Middle => 'heroicon-o-chevron-double-up',
            self::High => 'heroicon-o-fire'
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Low => 'info',
            self::Middle => 'success',
            self::High => 'danger'
        };
    }
}
