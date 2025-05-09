<?php

namespace App\Filament\Widgets\Statistic;

use App\Models\Expense;
use App\Models\Shift;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;

class ExpensesGraph extends ChartWidget
{
    public function getHeading(): string
    {
        return __('Money move');
    }

    // protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $start = now()->subMonths(11)->startOfMonth();
        $end = now()->endOfMonth();

        // Собираем список всех месяцев за период
        $months = collect(Carbon::parse($start)->monthsUntil($end))->map(fn ($date) => $date->format('Y-m'));

        // Расходы
        $expenses = Trend::model(Expense::class)
            ->between($start, $end)
            ->perMonth()
            ->sum('sum');

        $expenseData = collect($expenses)->mapWithKeys(fn ($item) => [
            Carbon::parse($item->date)->format('Y-m') => round($item->aggregate / 100, 2),
        ]);

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

        // Складываем cash и terminal
        $incomes = [];

        foreach ($months as $month) {
            $incomes[$month] = round((($cash[$month] ?? 0) + ($terminal[$month] ?? 0)) / 100, 2);
        }

        // Собираем приход и расход по месяцам
        $cashIn = [];
        $cashOut = [];

        foreach ($months as $month) {
            $cashIn[] = $incomes[$month] ?? 0;
            $cashOut[] = $expenseData[$month] ?? 0;
        }

        return [
            'labels' => $months->toArray(),
            'datasets' => [
                [
                    'label' => 'Приход (грн)',
                    'data' => $cashIn,
                    'backgroundColor' => 'rgba(30, 181, 33, 0.4)',
                ],
                [
                    'label' => 'Расход (грн)',
                    'data' => $cashOut,
                    'backgroundColor' => 'rgba(203, 27, 27, 0.4)',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
