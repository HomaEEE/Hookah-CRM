<?php

namespace Modules\Finance\Filament\Admin\Widgets\Statistic;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Modules\Finance\Enums\TransactionType;
use Modules\Finance\Models\Transaction;

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

        // 1. Создаем базовый массив месяцев с нулями ['2025-06' => 0, '2025-07' => 0]
        $months = collect(Carbon::parse($start)->monthsUntil($end))
            ->mapWithKeys(fn ($date) => [$date->format('Y-m') => 0]);

        // 2. Вспомогательная функция для получения данных (возвращает ассоциативный массив)
        $getTrendData = function (array $typeValues) use ($start, $end, $months) {
            $trend = Trend::query(Transaction::whereIn('type', $typeValues))
                ->between($start, $end)
                ->perMonth()
                ->sum('sum');

            $data = $months->toArray();

            foreach ($trend as $item) {
                $monthKey = Carbon::parse($item->date)->format('Y-m');
                if (isset($data[$monthKey])) {
                    // Сразу переводим из копеек в гривны/доллары
                    $data[$monthKey] = round($item->aggregate / 100, 2);
                }
            }

            return $data;
        };

        // 3. Получаем Приходы (только Income)
        $incomes = $getTrendData([TransactionType::Income->value]);

        // 4. Получаем Оттоки (суммируем Expenses и Withdrawals)
        $outflows = $getTrendData([
            TransactionType::Expense->value,
            TransactionType::Withdrawal->value,
        ]);

        // 5. Вычисляем Net Cashflow (Приход минус Отток)
        $netCashflow = [];
        foreach ($months as $month => $zeroValue) {
            $netCashflow[] = round($incomes[$month] - $outflows[$month], 2);
        }

        return [
            'labels' => array_keys($months->toArray()),
            'datasets' => [
                [
                    'label' => 'Net Cashflow',
                    'data' => $netCashflow,
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.4)',
                    'fill' => true,
                ],
            ],
        ];
    }
}
