<?php

namespace Modules\Manage\Filament\Admin\Resources\Inventory\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Modules\Manage\Enums\StateEnum;
use Modules\Manage\Models\InventoryItem;
use Modules\Manage\Models\InventorySale;

class InventoryStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $inStockStates = [
            StateEnum::ToTakeOut->value,
            StateEnum::ToSell->value,
            StateEnum::ToReturn->value,
            StateEnum::InRepair->value,
        ];

        // 1. Остаток (активы в наличии)
        $totalQuantity = (int) InventoryItem::whereIn('state', $inStockStates)->sum('quantity');
        $totalValue = (float) InventoryItem::whereIn('state', $inStockStates)
            ->sum(DB::raw('quantity * price'));

        // 2. Реальная сумма продаж (фактически оплаченная сумма)
        $realSales = (float) (InventorySale::selectRaw('
            SUM(CASE WHEN is_debt = 1 THEN paid_amount ELSE (quantity * price) END) as real_sales
        ')->value('real_sales') ?? 0);

        $totalSalesValue = (float) (InventorySale::sum(DB::raw('quantity * price')) ?? 0);

        // 3. Сумма долга (непогашенный остаток)
        $totalDebt = (float) (InventorySale::where('is_debt', true)
            ->where('is_debt_settled', false)
            ->sum('debt_amount') ?? 0);

        $unsettledDebtsCount = InventorySale::where('is_debt', true)
            ->where('is_debt_settled', false)
            ->count();

        return [
            Stat::make('Реальная сумма продаж', number_format($realSales / 100, 2, ',', ' ').' ₴')
                ->description('Всего продано на: '.number_format($totalSalesValue / 100, 2, ',', ' ').' ₴')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Остаток на складе', number_format($totalValue / 100, 2, ',', ' ').' ₴')
                ->description("В наличии: {$totalQuantity} шт.")
                ->icon('heroicon-o-cube')
                ->color('info'),

            Stat::make('Сумма долга', number_format($totalDebt / 100, 2, ',', ' ').' ₴')
                ->description($unsettledDebtsCount > 0 ? "Непогашенных долгов: {$unsettledDebtsCount}" : 'Все долги закрыты')
                ->icon('heroicon-o-banknotes')
                ->color($totalDebt > 0 ? 'danger' : 'success'),
        ];
    }
}
