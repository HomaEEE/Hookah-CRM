<?php

namespace App\Enums;

use BackedEnum;
use Filafly\Icons\Phosphor\Enums\Phosphor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum NavigationGroup: string implements HasLabel
{
    case Menu = 'Menu';
    case Manage = 'Manage';
    case Finance = 'Finance';
    case Place = 'Place';
    case Settings = 'Settings';
    case Data = 'Data';

    public function getLabel(): string
    {
        return match ($this) {
            self::Data => __('Data'),
            self::Menu => __('Menu'),
            self::Manage => __('Manage'),
            self::Finance => __('Finance'),
            self::Place => __('Place'),
            self::Settings => __('Settings'),
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Data => Phosphor::Database,
            self::Menu => Phosphor::QrCode,
            self::Manage => Phosphor::Package,
            self::Finance => Phosphor::Bank,
            self::Place => Phosphor::Building,
            self::Settings => Phosphor::Gear,
        };
    }
}
