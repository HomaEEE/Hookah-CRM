<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Enums\TransactionType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ВНИМАНИЕ: Укажи здесь реальные ID твоих счетов из таблицы bills
        $cashBillId = 1;      // ID счета для наличных
        $terminalBillId = 2;  // ID счета для безнала

        DB::table('shifts')->orderBy('id')->chunk(500, function ($shifts) use ($cashBillId, $terminalBillId) {
            $transactions = [];

            foreach ($shifts as $shift) {
                // Если смена еще не закрыта или пустая - пропускаем
                if ($shift->cash == 0 && $shift->terminal == 0) {
                    continue;
                }

                // Дата проведения: берем дату закрытия, если нет - дату создания
                $transactionDate = $shift->closed_at ?? $shift->created_at ?? now();

                // Формируем транзакцию для Наличных
                if ($shift->cash > 0) {
                    $transactions[] = [
                        'type' => TransactionType::Income->value,
                        'sum' => $shift->cash,
                        'user_id' => $shift->user_id,
                        'place_id' => $shift->place_id,
                        'bill_id' => $cashBillId,
                        'comment' => 'Выручка наличными #'.$shift->id,
                        'added_at' => $transactionDate,
                        'created_at' => $transactionDate,
                        'updated_at' => $transactionDate,
                    ];
                }

                // Формируем транзакцию для Терминала
                if ($shift->terminal > 0) {
                    $transactions[] = [
                        'type' => TransactionType::Income->value,
                        'sum' => $shift->terminal,
                        'user_id' => $shift->user_id,
                        'place_id' => $shift->place_id,
                        'bill_id' => $terminalBillId,
                        'comment' => 'Выручка по терминалу #'.$shift->id,
                        'added_at' => $transactionDate,
                        'created_at' => $transactionDate,
                        'updated_at' => $transactionDate,
                    ];
                }
            }

            // Массовая вставка пачки транзакций
            if (! empty($transactions)) {
                DB::table('transactions')->insert($transactions);
            }
        });
    }

    public function down(): void
    {
        // Удаляем только те приходы, которые были импортированы из смен
        DB::table('transactions')
            ->where('comment', 'like', 'Выручка % (Импорт из смены #%')
            ->delete();
    }
};
