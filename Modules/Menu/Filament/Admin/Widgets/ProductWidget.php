<?php

namespace Modules\Menu\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Menu\Models\Product;

class ProductWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $all = Product::count();
        $visible = Product::where('visible_menu', 1)->count();
        $hide = Product::where('visible_menu', 0)->count();

        return [
            Stat::make(__('All'), $all),
            Stat::make(__('Visibles'), $visible),
            Stat::make(__('Hidden'), $hide),

        ];
    }
}
