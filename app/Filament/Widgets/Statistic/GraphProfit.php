<?php

namespace App\Filament\Widgets\Statistic;

use Filament\Widgets\ChartWidget;

use App\Models\Expense;
use App\Models\Shift;
use Filament\Facades\Filament;
use SaKanjo\EasyMetrics\Metrics\Trend;
use SaKanjo\EasyMetrics\Metrics\Value;

class GraphProfit extends ChartWidget
{

    public function getHeading(): string
    {
        return __('Profit Statistic');
    }

    protected function getData(): array
    {
        [$labels, $cash] =
            Trend::make(Shift::class)
            ->sumByMonths('cash');

        // $terminal =
        //     Trend::make(Shift::class)
        //     ->sumByMonths('terminal');

        // Value::make(Shift::where('place_id', Filament::getTenant()->id))
        //         ->rangesFromOptions($this->getFilters())
        //         ->sum('cash');

        return [
            'datasets' => [
                [
                    'label' => _('Cash'),
                    'data' => $cash,
                ],

                // [
                //     'label' => _('terminal'),
                //     'data' => $terminal,

                // ]
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
