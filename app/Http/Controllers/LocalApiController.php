<?php

namespace App\Http\Controllers;

use App\Models\{Withdrawal, Tip, Supply, Product, Place, Expense, Category, Ingredient, Shift, Workshop};
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class LocalApiController extends Controller
{

    public function getLocalWithdrawal($place_id)
    {
        $withdrawal = Withdrawal::select('sum')
            ->where('place_id', $place_id)->where('bill_id', 0)
            ->whereDate('created_at', Carbon::today());
        return $withdrawal->sum('sum');
    }

    public function getLocalTips($place_id)
    {
        $tips = Tip::select('tips')
            ->where('place_id', $place_id)
            ->whereDate('created_at', Carbon::today());
        $TOTAL = $tips->sum('tips');

        return $TOTAL;
    }

    public function getLocalExpenses($place_id)
    {
        $cash_expense = Expense::select('sum')->where(
            [
                ['bill_id', 0],
                ['place_id', $place_id],
            ]
        )
            ->whereDate('added_at', Carbon::today());

        return $cash_expense->sum('sum');
    }

}
