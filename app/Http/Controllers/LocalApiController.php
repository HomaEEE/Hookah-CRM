<?php

namespace App\Http\Controllers;

use App\Models\Tip;
use Auth;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Modules\Finance\Models\Expense;
use Modules\Finance\Models\Income;
use Modules\Finance\Models\Shift;
use Modules\Finance\Models\ShiftUser;
use Modules\Finance\Models\Withdrawal;

class LocalApiController extends Controller
{
    public function getLocalWithdrawal($place_id)
    {
        $withdrawal = Withdrawal::select('sum')
            ->where('place_id', $place_id)->where('bill_id', 0)
            ->whereDate('created_at', Carbon::today());

        return $withdrawal->sum('sum') / 100;
    }

    public function getLocalTips($place_id)
    {
        $tips = Tip::select('tips')
            ->where('place_id', $place_id)
            ->whereDate('created_at', Carbon::today());
        $TOTAL = $tips->sum('tips') / 100;

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

        return $cash_expense->sum('sum') / 100;
    }

    public function getLocalIncomes($place_id)
    {
        $cash_incomes = Income::select('sum')
            ->where('place_id', $place_id)
            ->whereDate('created_at', Carbon::today());

        return $cash_incomes->sum('sum') / 100;
    }

    public function openShift($data)
    {
        $old_shift = Shift::latest('id')->where('place_id', Filament::getTenant()->id)->select('shift')->first();
        $shift = Shift::create(
            [
                'place_id' => $data['place_id'],
                'created_at' => Carbon::now(),
                'user_id' => Auth::user()->id,
                'old_shift' => $old_shift->shift,
            ]
        );

        ShiftUser::create([
            'shift_id' => $shift->id,
            'user_id' => Auth::user()->id,
            'wage_id' => Auth::user()->wage_id,
        ]);
    }

    public function AddUserToShift($data)
    {

        dd(Shift::latest('id')->where([['place_id', Filament::getTenant()->id], ['created_at', Carbon::today()]]));
        $addToShift = ShiftUser::create([
            'shift_id' => Shift::latest('id')->where([['place_id', Filament::getTenant()->id], ['created_at', Carbon::today()]])->select('id'),
            'user_id' => Auth::user()->id,
            'wage_id' => Auth::user()->wage_id,
        ]
        );
    }
}
