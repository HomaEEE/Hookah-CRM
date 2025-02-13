<?php

namespace App\Filament\Widgets\Test;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use Auth;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class UserShift extends BaseWidget
{
    use HasWidgetShield;

    protected function getHeading(): ?string
    {
        return __('User Shift');
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Shift'), Auth::user()->shift_day)
        ];
    }
}
