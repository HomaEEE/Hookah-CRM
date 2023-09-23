<?php

namespace App\Filament\Widgets\Statistic;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

use App\Models\Shift;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;

class GraphShifts extends ChartWidget
{

    public function getHeading(): string
    {
        return __('Shifts');
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $data = Trend::query( Shift::query()->where('place_id', Filament::getTenant()->id) )
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->sum('terminal','cash');

        return [
            'datasets' => [
                [
                    'label' => __('Shift'),
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
