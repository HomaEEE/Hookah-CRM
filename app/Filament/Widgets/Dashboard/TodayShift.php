<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\{Place, Shift, Tip, Expense};
use Auth;
use Carbon\Carbon;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Facades\Filament;

class TodayShift extends BaseWidget
{
    use HasWidgetShield;

    protected static ?string $pollingInterval = null;

    protected $listeners = ['updateTodayShift' => '$refresh'];

    protected function getStats(): array
    {
            $shift = Shift::where('place_id', Filament::getTenant()->id)->select('shift', 'created_at')->latest()->first();

            $tipsShift = Tip::where('place_id', Filament::getTenant()->id)
            ->whereDate('created_at', Carbon::today())->value('tips');

            $expenseShift = Expense::where('place_id', Filament::getTenant()->id)->whereDate('added_at', Carbon::today())->sum('sum');

            return [
                Stat::make(__('Shift Poster'), $shift != null ? $shift->shift : 0)
                ->icon('heroicon-o-lock-open')
                ->description($shift->created_at->since()),

                Stat::make(__('Tips'), $tipsShift != null ? $tipsShift : 0)
                ->icon('heroicon-o-banknotes'),

                Stat::make(__('Expenses'), $expenseShift != null ? $expenseShift/100 : 0)
                ->icon('heroicon-o-arrow-up-on-square-stack')
            ];
    }
}
