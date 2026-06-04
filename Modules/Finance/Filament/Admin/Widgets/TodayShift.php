<?php

namespace Modules\Finance\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Finance\Enums\TransactionType;
use Modules\Finance\Models\Shift;
use Modules\Finance\Models\Transaction;

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

        $WithdrawalShift = Transaction::where('type', TransactionType::Withdrawal->value)
            ->whereDate('added_at', Carbon::today())->sum('sum');

        $expenses = Transaction::where('type', TransactionType::Expense->value)
            ->whereDate('added_at', Carbon::today())->sum('sum');

        return [
            Stat::make(__('Shift Poster'), $shift != null ? $shift->shift : 0)
                ->icon('heroicon-o-lock-open')
                ->description($shift != null ? $shift->created_at->since() : Carbon::today()->since()),

            Stat::make(__('Withdrawals'), $WithdrawalShift != null ? $WithdrawalShift / 100 : 0)
                ->icon('heroicon-o-banknotes')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', { filter: 'processed' })",
                ]),
            Stat::make(__('Expenses'), $expenses != null ? $expenses / 100 : 0)
                ->icon('heroicon-o-arrow-up-on-square-stack'),
        ];
    }
}
