<?php

namespace App\Filament\Widgets\Statistic;

use Filament\Widgets\ChartWidget;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use SaKanjo\EasyMetrics\Metrics\Trend;
use SaKanjo\EasyMetrics\Enums\Range;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;


class GraphExpenses extends ChartWidget
{

    public function getHeading(): string
    {
        return __('Graph Expenses');
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
                    'label' => __('Expenses'),
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
        ];
    }
}
