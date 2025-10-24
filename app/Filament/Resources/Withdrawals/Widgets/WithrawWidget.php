<?php

namespace App\Filament\Resources\Withdrawals\Widgets;

use App\Models\Withdrawal;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WithrawWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $all = Withdrawal::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('sum');
        $card = Withdrawal::where('bill_id', 1)
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('sum');
        $cash = Withdrawal::where('bill_id', 0)
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('sum');

        return [
            Stat::make(__('Mounth'), $all / 100),
            Stat::make(__('Card'), $card / 100),
            Stat::make(__('Cash'), $cash / 100),

        ];
    }
}
