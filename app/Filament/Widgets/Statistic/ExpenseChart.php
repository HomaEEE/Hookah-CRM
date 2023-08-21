<?php

namespace App\Filament\Widgets\Statistic;

use App\Models\Expense;
use Filament\Widgets\ChartWidget;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    public $place_id;

    public function __construct($place_id)
    {
        $this->place_id = $place_id;
    }

    protected function getData(): array
    {
        $data = Trend::model(Expense::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();


        return [
            'datasets' => [
                [
                    'label' => 'Blog posts',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
