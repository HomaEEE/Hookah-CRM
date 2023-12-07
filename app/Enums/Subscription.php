<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Subscription: string implements HasLabel
{
    case  None = '0';
    case  Day= '1';
    case  Week = '2';
    case  TwoWeek = '3';
    case  FourWeek = '4';
    case  Mounth = '5';
    case  TwoMounth = '6';
    case  SixMounth = '7';
    case  Year = '8';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::None => __('None'),
            self::Day => __('Day'),
            self::Week => __('Week'),
            self::TwoWeek => __('Two Week'),
            self::FourWeek => __('Four Week'),
            self::Mounth => __('Mounth'),
            self::TwoMounth => __('Two Mounth'),
            self::SixMounth => __('Six Mounth'),
            self::Year => __('Year'),
        };
    }
}

