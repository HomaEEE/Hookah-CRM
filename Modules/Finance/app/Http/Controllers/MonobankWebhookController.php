<?php

namespace Modules\Finance\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Finance\Enums\TransactionType;
use Modules\Finance\Models\Transaction;
use Modules\Place\Models\Reqvizite;

class MonobankWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->input('data.statementItem') ?? $request->all();

        if (empty($data['id'])) {
            return response()->json(['status' => 'ignored']);
        }

        // --- 1. АВТОМАТИЧЕСКОЕ ОБНОВЛЕНИЕ РЕКВИЗИТОВ ---
        $counterIban = $data['counterIban'] ?? null;
        $counterName = $data['counterName'] ?? null;

        if ($counterIban && $counterName) {
            $cleanIban = Str::startsWith($counterIban, 'UA')
                ? Str::substr($counterIban, 2)
                : $counterIban;

            // ModelsReqvizite::where('IBAN', $cleanIban)->update([
            //     'own' => $counterName
            // ]);

            Reqvizite::where('IBAN', $cleanIban)->whereNull('own')->update(['own' => $counterName]);
        }
        // ----------------------------------------------

        $rawAmount = $data['amount'];
        $type = $rawAmount > 0 ? TransactionType::Income : TransactionType::Expense;

        $fullDescription = $data['description'];
        if (! empty($data['comment'])) {
            $fullDescription .= ' ('.$data['comment'].')';
        }

        $transaction = Transaction::updateOrCreate(
            ['external_api_id' => $data['id']],
            [
                'type' => $type->value,
                'sum' => abs($rawAmount),
                'bill_id' => 2,
                'description' => $fullDescription,
                'added_at' => Carbon::createFromTimestamp($data['time']),
                'is_reviewed' => Transaction::where('external_api_id', $data['id'])->exists()
                    ? Transaction::where('external_api_id', $data['id'])->value('is_reviewed')
                    : false,
                'comment' => $data['comment'],
            ]
        );

        return response()->json(['status' => 'ok']);
    }
}
