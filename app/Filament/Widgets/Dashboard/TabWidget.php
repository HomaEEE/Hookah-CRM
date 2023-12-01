<?php

namespace App\Filament\Widgets\Dashboard;

use Kenepa\MultiWidget\MultiWidget;

class TabWidget extends MultiWidget
{
    public array $widgets = [
        ExpensesShift::class,
        BookingWidget::class,
        TaskWidget::class,
        SupplyToPay::class,
    ];
}