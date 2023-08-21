<?php

namespace App\Filament\Widgets\Shift;

use App\Http\Controllers\PosterApiController as PosterApi;
use App\Models\Place;
use Auth;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PosterShift extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected function getCards(): array
    {
        return collect(Place::whereIn('id', Auth::user()->place->pluck('id'))->get())->map(function ($place) {
            $posterTodayShift = (new PosterApi())->getTodayShift($place->id);

            return Stat::make($place->city, $posterTodayShift != null ? $posterTodayShift['payed_sum_sum'] / 100 : 0)
                ->description('Касса')
                ->descriptionIcon('heroicon-s-clock');
        })->toArray();
    }
}
