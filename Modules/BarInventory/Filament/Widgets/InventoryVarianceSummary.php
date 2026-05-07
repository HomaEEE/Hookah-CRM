<?php

namespace Modules\BarInventory\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\BarInventory\Models\InventoryLine;
use Modules\BarInventory\Models\InventorySession;
use Modules\BarInventory\Enums\SessionStatus;

class InventoryVarianceSummary extends BaseWidget
{
    protected function getStats(): array
    {
        $latestSession = InventorySession::where('status', SessionStatus::Completed)
            ->latest('completed_at')
            ->first();

        if (!$latestSession) {
            return [
                Stat::make('Latest Variance', 'N/A')
                    ->description('No completed sessions found')
                    ->color('gray'),
            ];
        }

        $totalVariance = InventoryLine::where('session_id', $latestSession->id)
            ->sum('variance');

        $lossCount = InventoryLine::where('session_id', $latestSession->id)
            ->where('variance', '<', 0)
            ->count();

        return [
            Stat::make('Latest Session Variance', number_format($totalVariance, 2) . ' units')
                ->description($latestSession->completed_at?->diffForHumans() ?? 'Just now')
                ->color($totalVariance < 0 ? 'danger' : 'success')
                ->icon($totalVariance < 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up'),
            
            Stat::make('Products with Loss', $lossCount)
                ->description('Total products with negative variance')
                ->color($lossCount > 0 ? 'warning' : 'success'),
        ];
    }
}
