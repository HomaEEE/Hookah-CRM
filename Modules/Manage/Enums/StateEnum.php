<?php

namespace Modules\Manage\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use ToneGabes\Filament\Icons\Enums\Phosphor;

enum StateEnum: string implements HasColor, HasIcon, HasLabel
{
    case ToTakeOut = '1';
    case ToSell = '2';
    case ToReturn = '3';
    case Sold = '21';
    case WrittenOff = '22';
    case Damaged = '23';
    case Lost = '24';
    case InRepair = '25';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ToTakeOut => __('To Take Out'),
            self::ToSell => __('To Sell'),
            self::ToReturn => __('To Return'),
            self::Sold => __('Sold'),
            self::WrittenOff => __('Written Off'),
            self::Damaged => __('Damaged'),
            self::Lost => __('Lost'),
            self::InRepair => __('In Repair'),
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::ToTakeOut => Phosphor::Truck,
            self::ToSell => Phosphor::CoinVertical,
            self::ToReturn => Phosphor::ArrowClockwise,
            self::Sold => Phosphor::Coin,
            self::WrittenOff => Phosphor::X,
            self::Damaged => Phosphor::HeartHalf,
            self::Lost => Phosphor::Ghost,
            self::InRepair => Phosphor::Wrench,
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ToTakeOut => 'success',
            self::ToSell => 'info',
            self::ToReturn => 'heroicon-m-credit-card',
            self::Sold => 'heroicon-m-credit-card',
            self::WrittenOff => 'heroicon-m-credit-card',
            self::Damaged => 'heroicon-m-credit-card',
            self::Lost => 'heroicon-m-credit-card',
            self::InRepair => 'heroicon-m-credit-card',
        };
    }
}
