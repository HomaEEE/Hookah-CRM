<?php

namespace Modules\Finance\Filament\Admin\Widgets\UserStat;

use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Finance\Models\ShiftUser;

class UserStatShift extends BaseWidget
{
    protected function getStats(): array
    {

        $all_sfhits = ShiftUser::where('user_id', auth()->user()->id)->count('days');
        $mounth_sfhits = ShiftUser::where('user_id', auth()->user()->id)
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('days');
        $mounth_cashout = ShiftUser::where('user_id', auth()->user()->id)
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('cashout');

        return [
            Stat::make('Всего смен', $all_sfhits),
            Stat::make('Смен в месяц', $mounth_sfhits),
            Stat::make('Ставка за месяц', $mounth_cashout / 100),

        ];
    }
}
