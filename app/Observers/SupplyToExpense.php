<?php

namespace App\Observers;

use App\Models\{Supply, Expense, ExpenseCategory};
use Auth;
use Filament\Facades\Filament;

class SupplyToExpense
{
    public function created(Supply $supply): void
    {
        if($supply->pay == true)
        {
            Expense::create(
                [
                    'user_id' => Auth::user()->id,
                    'place_id' => $supply->place_id,
                    'supply_id' => $supply != null ? $supply->id : 0,
                    'category_id' => ExpenseCategory::where(['place_id' => Filament::getTenant()->id, 'name' => 'Поставки'])->get('id'),
                    'sum' => $supply->sum,
                    'type_pay' => $supply->type_pay,
                    'comment' => $supply->comment,
                ]
            );
        }
    }

    public function updated(Supply $supply): void
    {
        if($supply->pay == true)
        {
            Expense::updateOrCreate(
                [

                    'place_id' => $supply->place_id,
                    'supply_id' => $supply != null ? $supply->id : 0,
                    'sum' => $supply->sum,
                ],
                [
                    'user_id' => Auth::user()->id,
                    'category_id' =>35,
                    'type_pay' => $supply->type_pay,
                    'comment' => $supply->comment,
                ]
            );
        }
        else if ($supply->pay == false)
        {
            Expense::where([
                'supply_id' => $supply->id,
                'sum' => $supply->sum
                ])->delete();
        }

    }

    public function deleted(Supply $supply): void {
        Expense::where('supply_id', $supply->id)->delete();
    }
}
