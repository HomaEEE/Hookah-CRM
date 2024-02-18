<?php

namespace App\Filament\Resources\WithdrawalResource\Pages;

use App\Filament\Resources\WithdrawalResource;
use App\Models\Bill;
use Auth;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Components\Tab;

class ManageWithdrawals extends ManageRecords
{
    protected static string $resource = WithdrawalResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make()->modalWidth('xs')->createAnother(false),
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
