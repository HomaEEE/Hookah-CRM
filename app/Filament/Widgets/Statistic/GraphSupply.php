<?php

namespace App\Filament\Widgets\Statistic;

use App\Models\Supplier;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

use App\Models\Supply;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;

class GraphSupply extends ChartWidget
{

    public function getHeading(): string
    {
        return __('Supplies');
    }

    // protected function getFilters(): ?array
    // {
    //     $suppliers= Supplier::where('place_id', Filament::getTenant()->id)->visible()->pluck('company', 'id');
    //     return $suppliers;
    // }

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $data = Trend::query( Supply::where('place_id', Filament::getTenant()->id) )
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->sum('sum');

        return [
            'datasets' => [
                [
                    'label' => 'Расход',
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
