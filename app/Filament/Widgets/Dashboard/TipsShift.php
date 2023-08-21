<?php

namespace App\Filament\Widgets\Dashboard;

use Auth;

use App\Models\{Place, Tip};
use Carbon\Carbon;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TipsShift extends BaseWidget
{
   use HasWidgetShield;

    protected function getStats(): array
    {
        return collect(Place::whereIn('id', Auth::user()->place->pluck('id'))->get())->map(function ($place) {
            $tipsShift = Tip::where('place_id', $place->id)
            ->whereDate('created_at', Carbon::today())->value('tips');

            return Stat::make($place->city, $tipsShift != null ? $tipsShift : 0)
                ->description('Чаевые')
                ->descriptionIcon('heroicon-s-banknotes');
        })->toArray();
    }
}
