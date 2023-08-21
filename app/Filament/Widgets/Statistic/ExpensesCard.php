<?php

namespace App\Filament\Widgets\Shift;

use App\Http\Controllers\PosterApiController as PosterApi;
use App\Models\{Place, Expense};
use Auth;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpensesCard extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

    protected function getCards(): array
    {
        return collect(Place::whereIn('id', Auth::user()->place->pluck('id'))->get())->map(function ($place) {

            $expenes = Expense::where('place_id', $place->id)->whereDate('added_at', Carbon::now()->format('M'));

            return Stat::make($place->city, $posterTodayShift != null ? $posterTodayShift['payed_sum_sum'] / 100 : 0)
                ->description('Касса')
                ->descriptionIcon('heroicon-s-clock');
        })->toArray();
    }
}
