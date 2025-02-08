<?php

namespace App\Filament\Widgets\Dashboard;

use App\Http\Controllers\PosterApiController as PosterApi;
use App\Models\Place;
use Auth;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PosterShift extends BaseWidget
{
    protected static ?string $pollingInterval = '600s';

    protected function getHeading(): ?string
    {
        return __('Poster');
    }

    protected function getCards(): array
    {
        $posterTodayShift = (new PosterApi())->getTodayShift(Filament::getTenant()->id);
        return [
            Stat::make(__('Shift'), $posterTodayShift != null ? $posterTodayShift['payed_sum_sum'] / 100 : 0)
            ->icon('heroicon-o-calendar-days'),

            Stat::make(__('Cash'), $posterTodayShift != null ? $posterTodayShift['payed_cash_sum'] / 100 : 0)
            ->icon('heroicon-o-banknotes'),

            Stat::make(__('Terminal'), $posterTodayShift != null ? $posterTodayShift['payed_card_sum'] / 100 : 0)
            ->icon('heroicon-o-credit-card')
        ];
    }
}
