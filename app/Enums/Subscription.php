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

    public function getLabel(): ?string
    {
        return $this->name;
    }
}

