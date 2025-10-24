<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Expense;
use App\Models\Shift;
use App\Models\Withdrawal;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TodayShift extends BaseWidget
{
    // use HasWidgetShield;

    protected function getHeading(): ?string
    {
        return __('Shift');
    }

    protected function getStats(): array
    {
        $shift = Shift::where('place_id', Filament::getTenant()->id)->select('shift', 'created_at')->latest()->first();

        $WithdrawalShift = Withdrawal::where('place_id', Filament::getTenant()->id)
            ->whereDate('created_at', Carbon::today())->value('sum');

        $expenseShift = Expense::where('place_id', Filament::getTenant()->id)->whereDate('added_at', Carbon::today())->sum('sum');

        return [
            Stat::make(__('Shift Poster'), $shift != null ? $shift->shift : 0)
                ->icon('heroicon-o-lock-open')
                ->description($shift != null ? $shift->created_at->since() : Carbon::today()->since()),

            Stat::make(__('Withdrawals'), $WithdrawalShift != null ? $WithdrawalShift : 0)
                ->icon('heroicon-o-banknotes')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
                ]),
            Stat::make(__('Expenses'), $expenseShift != null ? $expenseShift / 100 : 0)
                ->icon('heroicon-o-arrow-up-on-square-stack'),
        ];
    }
}
