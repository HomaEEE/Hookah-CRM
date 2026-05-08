<?php

namespace Modules\Finance\Filament\Admin\Resources\Withdrawals\Pages;

use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Modules\Finance\Filament\Admin\Resources\Withdrawals\Widgets\WithrawWidget;
use Modules\Finance\Filament\Admin\Resources\Withdrawals\WithdrawalResource;
use Modules\Finance\Models\Bill;

class ManageWithdrawals extends ManageRecords
{
    protected static string $resource = WithdrawalResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make()->modalWidth('xs')->createAnother(false),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WithrawWidget::class,
        ];
    }

    public function getTabs(): array
    {
        $tabs = [null => Tab::make(__('All'))];

        $bills = Bill::where('place_id', Filament::getTenant()->id)->get();

        foreach ($bills as $bill) {
            $tabs[$bill->name] = Tab::make()->query(function ($query) use ($bill) {
                $query->where('bill_id', $bill->id);
            });
        }

        return $tabs;
    }
}
