<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Place;
use App\Models\Shift;
use Auth;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class OpenShift extends BaseWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    protected static ?string $pollingInterval = '1000s';

    protected function getStats(): array
    {
        return collect(Place::whereIn('id', Auth::user()->place->pluck('id'))->get())->map(function ($place) {
            $shift = Shift::where('place_id', $place->id)->select('shift')->latest()->first();

            return Stat::make($place->city, $shift != null ? $shift->shift : 0)
                ->description('Открытие смены')
                ->descriptionIcon('heroicon-s-cloud-arrow-up');
        })->toArray();
    }
}
