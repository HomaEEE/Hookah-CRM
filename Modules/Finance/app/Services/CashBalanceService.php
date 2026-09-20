<?php

namespace Modules\Finance\app\Services;

use Carbon\Carbon;
use Modules\Finance\Enums\BillType;
use Modules\Finance\Enums\TransactionType;
use Modules\Finance\Models\Shift;
use Modules\Finance\Models\Transaction;

class CashBalanceService
{
    public function getCurrentBalance(int $placeId, ?int $posterCash = null, ?int $wages = null): float
    {
        $posterCash = $posterCash ?? 0;
        $wages = $wages ?? 0;

        $lastClosedShift = Shift::where('place_id', $placeId)
            ->whereNotNull('created_at')
            ->latest('created_at')
            ->select('shift')
            ->first();

        $initialBalance = $lastClosedShift ? $lastClosedShift->shift : 0;

        $netCashflow = Transaction::where('place_id', $placeId)
            ->where('bill_id', BillType::CASH->value)
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
            ->value('net_balance') / 100 ?? 0;

        return $initialBalance + $posterCash + $netCashflow - $wages;

    }
}
