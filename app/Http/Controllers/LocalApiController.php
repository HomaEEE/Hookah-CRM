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
    public function getLocalWithdrawal($place_id)
    {
        $withdrawal = Transaction::select('sum')->where(
            ['type' => TransactionType::Withdrawal],
            ['bill_id' => BillType::CASH]
        )
            ->whereDate('created_at', Carbon::today());

        return $withdrawal->sum('sum');
    }

    public function getLocalTips($place_id)
    {
        $tips = Tip::select('tips')->where([
            ['created_at' => Carbon::today()],
        ])->sum('tips') / 100;

        return $tips;
    }

    public function getLocalExpenses($place_id)
    {
        $cash_expense = Transaction::select('sum')->where(
            [
                ['bill_id' => BillType::CASH],
                ['type' => TransactionType::Expense],
            ]
        )
            ->whereDate('added_at', Carbon::today())->sum('sum');

        return $cash_expense;
    }

    public function getLocalIncomes($place_id)
    {
        $cash_incomes = Transaction::select('sum')->where(
            ['bill_id' => BillType::CASH],
            ['type' => TransactionType::Income],
            ['added_at' => Carbon::today()])->sum('sum');

        return $cash_incomes;
    }

    public function getCurrentCashBalance(int $placeId, int $cashBillId, $posterCash = null): float
    {
        // 1. Находим последнюю закрытую смену
        $lastClosedShift = Shift::where('place_id', $placeId)
            ->whereNotNull('created_at')
            ->latest('created_at')
            ->select('shift')
            ->first();

        $initialBalance = $lastClosedShift ? $lastClosedShift->shift : 0;

        $netCashflowKopecks = Transaction::where('place_id', $placeId)
            ->where('bill_id', $cashBillId)
            ->whereDate('added_at', Carbon::today())
            ->selectRaw('SUM(
            CASE
                WHEN type IN (?, ?) THEN sum
                WHEN type IN (?, ?, ?) THEN -sum
                WHEN type = ? THEN sum
                ELSE 0
            END
        ) as net_balance', [
                TransactionType::Income->value,
                TransactionType::Loan->value,

                TransactionType::Expense->value,
                TransactionType::Withdrawal->value,
                TransactionType::Repayment->value,
                TransactionType::Transfer->value,
            ])
            ->value('net_balance') ?? 0;

        return $initialBalance + $posterCash + $netCashflowKopecks;
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
