<?php

namespace App\Http\Controllers;

use App\Models\Tip;
use Auth;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Modules\Finance\Enums\BillType;
use Modules\Finance\Enums\TransactionType;
use Modules\Finance\Models\Shift;
use Modules\Finance\Models\ShiftUser;
use Modules\Finance\Models\Transaction;

class LocalApiController extends Controller
{
    public function getLocalTips($place_id)
    {
        $tips = Tip::select('tips')->where('created_at', Carbon::today())->sum('tips') / 100;

        return $tips ?? 0;
    }

    // public function getCurrentCashBalance(int $placeId, ?int $posterCash = null, ?int $wages = null): float
    // {
    //     $posterCash = $posterCash ?? 0;
    //     $wages = $wages ?? 0;

    //     $lastClosedShift = Shift::where('place_id', $placeId)
    //         ->whereNotNull('created_at')
    //         ->latest('created_at')
    //         ->select('shift')
    //         ->first();

    //     $initialBalance = $lastClosedShift ? $lastClosedShift->shift : 0;

    //     $netCashflow = Transaction::where('place_id', $placeId)
    //         ->where('bill_id', BillType::CASH)
    //         ->whereDate('added_at', Carbon::today())
    //         ->selectRaw('SUM(
    //         CASE
    //             WHEN type IN (?, ?) THEN sum
    //             WHEN type IN (?, ?, ?) THEN -sum
    //             WHEN type = ? THEN sum
    //             ELSE 0
    //         END
    //     ) as net_balance', [
    //             TransactionType::Income->value,
    //             TransactionType::Loan->value,

    //             TransactionType::Expense->value,
    //             TransactionType::Withdrawal->value,
    //             TransactionType::Repayment->value,

    //             TransactionType::Transfer->value,
    //         ])
    //         ->value('net_balance') / 100 ?? 0;

    //     return $initialBalance + $posterCash + $netCashflow - $wages;

    // }

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
