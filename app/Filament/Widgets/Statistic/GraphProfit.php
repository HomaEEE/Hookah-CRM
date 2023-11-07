<?php

namespace App\Filament\Widgets\Statistic;

use Filament\Widgets\ChartWidget;

use App\Models\Expense;
use Filament\Facades\Filament;
use SaKanjo\EasyMetrics\Metrics\Trend;

class GraphProfit extends ChartWidget
{

    public function getHeading(): string
    {
        return __('Statistic');
    }

    protected function getData(): array
    {
    [$labels, $data] = Trend::make(Expense::where('place_id', Filament::getTenant()->id))
            ->range($this->filter)
            ->rangesFromOptions($this->getFilters())
            ->sumByMonths('sum');

        return [
            'datasets' => [
                [
                    'label' => 'Profit',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        return [
            15 => __('15 Days'),
            30 => __('Mounth'),
            60 => __('2 Mounths'),
            365 => __('Year')
        ];
    }
}
