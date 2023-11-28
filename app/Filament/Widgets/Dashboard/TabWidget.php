<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Widgets\Shift\ExpensesShift;
use App\Models\Expense;
use App\Models\Task;
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