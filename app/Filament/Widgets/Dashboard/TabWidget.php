<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Task;
use Kenepa\MultiWidget\MultiWidget;

class TabWidget extends MultiWidget
{
    public array $widgets = [
        BookingWidget::class,
        TaskWidget::class,
        SupplyToPay::class
    ];
}