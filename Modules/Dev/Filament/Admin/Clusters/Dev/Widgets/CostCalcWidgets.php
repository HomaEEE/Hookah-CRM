<?php

namespace Modules\Dev\Filament\Admin\Clusters\Dev\Widgets;

use App\Models\Expense;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CostCalcWidgets extends BaseWidget
{
    protected function getStats(): array
    {

        $rent = Expense::where('comment', 'Аренда помещения')->latest()->first();
        $services = Expense::where('category_id', '3')
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('sum');
        $other = Expense::whereIn('category_id', ['4', ''])
            ->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('sum');

        return [
            Stat::make('Аренда', $rent->sum),
            Stat::make('Коммунальные расходы', $services / 100),
            Stat::make('Прочие расходы', $other),

        ];
    }
}
