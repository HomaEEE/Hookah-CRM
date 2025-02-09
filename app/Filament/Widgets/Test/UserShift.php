<?php

namespace App\Filament\Widgets\Test;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use Auth;

class UserShift extends BaseWidget
{

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
