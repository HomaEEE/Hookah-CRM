<?php

namespace Modules\Finance\Filament\Admin\Widgets\Statistic;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Modules\Finance\Enums\TransactionType;
use Modules\Finance\Models\Transaction;

class ExpensesGraph extends ChartWidget
{
    public function getHeading(): string
    {
        return __('Money move');
    }

    // protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $start = now()->subMonths(11)->startOfMonth();
        $end = now()->endOfMonth();

        // 1. Создаем шаблонный массив месяцев с нулевыми значениями: ['2025-06' => 0, '2025-07' => 0, ...]
        $months = collect(Carbon::parse($start)->monthsUntil($end))
            ->mapWithKeys(fn ($date) => [$date->format('Y-m') => 0]);

        // 2. Вспомогательная функция (Closure), чтобы не дублировать код запросов Trend
        $getTrendData = function (TransactionType $type) use ($start, $end, $months) {
            $trend = Trend::query(Transaction::where('type', $type->value))
                ->between($start, $end)
                ->perMonth()
                ->sum('sum');

            // Копируем базовый массив с нулями
            $data = $months->toArray();

            // Заполняем реальными данными из БД
            foreach ($trend as $item) {
                $monthKey = Carbon::parse($item->date)->format('Y-m');
                if (isset($data[$monthKey])) {
                    // Делим на 100, если суммы хранятся в БД в копейках/центах
                    $data[$monthKey] = round($item->aggregate / 100, 2);
                }
            }

            // Возвращаем только массив значений строго по порядку для графика
            return array_values($data);
        };

        // 3. Формируем финальный массив для Chart.js
        return [
            'labels' => array_keys($months->toArray()), // Ось X: '2025-06', '2025-07' и т.д.
            'datasets' => [
                [
                    'label' => 'Приход',
                    'data' => $getTrendData(TransactionType::Income),
                    'backgroundColor' => 'rgba(30, 181, 33, 0.6)', // Зеленый
                ],
                [
                    'label' => 'Расход',
                    'data' => $getTrendData(TransactionType::Expense),
                    'backgroundColor' => 'rgba(203, 27, 27, 0.6)', // Красный
                ],
                [
                    'label' => 'Инкассация',
                    'data' => $getTrendData(TransactionType::Withdrawal),
                    'backgroundColor' => 'rgba(245, 158, 11, 0.6)', // Оранжевый
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
