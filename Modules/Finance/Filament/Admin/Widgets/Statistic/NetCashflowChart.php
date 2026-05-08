<?php

namespace Modules\Finance\Filament\Admin\Widgets\Statistic;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Modules\Finance\Models\Expense;
use Modules\Finance\Models\Shift;

class NetCashflowChart extends ChartWidget
{
    protected ?string $heading = 'Net Cashflow';

    protected ?string $maxHeight = '250px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $start = now()->subMonths(11)->startOfMonth();
        $end = now()->endOfMonth();

        $expenses = Trend::model(Expense::class)
            ->between(start: $start, end: $end)
            ->perMonth()
            ->sum('sum');

        // Приход - cash
        $cashData = Trend::model(Shift::class)
            ->between($start, $end)
            ->perMonth()
            ->sum('cash');

        $cash = collect($cashData)->mapWithKeys(fn ($item) => [
            Carbon::parse($item->date)->format('Y-m') => $item->aggregate,
        ]);

        // Приход - terminal
        $terminalData = Trend::model(Shift::class)
            ->between($start, $end)
            ->perMonth()
            ->sum('terminal');

        $terminal = collect($terminalData)->mapWithKeys(fn ($item) => [
            Carbon::parse($item->date)->format('Y-m') => $item->aggregate,
        ]);

        $months = collect(Carbon::parse($start)->monthsUntil($end))->map(fn ($date) => $date->format('Y-m'));

        $incomes = [];

        foreach ($months as $month) {
            $incomes[$month] = round((($cash[$month] ?? 0) + ($terminal[$month] ?? 0)), 2);
        }

        $expenseData = collect($expenses)->mapWithKeys(fn ($item) => [Carbon::parse($item->date)->format('Y-m') => $item->aggregate]);

        $net = $months->map(fn ($m) => round(($incomes[$m] ?? 0) / 100, 2) - round(($expenseData[$m] ?? 0) / 100, 2));

        return [
            'labels' => $months->toArray(),
            'datasets' => [[
                'label' => 'Net Cashflow',
                'data' => $net,
                'borderColor' => 'rgba(54, 162, 235, 1)',
                'backgroundColor' => 'rgba(54, 162, 235, 0.4)',
                'fill' => true,
            ]],
            'legend' => [
                'show' => 'false',
            ],
        ];
    }
}
