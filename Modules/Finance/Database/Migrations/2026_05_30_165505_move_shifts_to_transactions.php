<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Modules\Finance\Enums\BillType;
use Modules\Finance\Enums\TransactionType;
use Symfony\Component\Console\Output\ConsoleOutput;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        $output = new ConsoleOutput;

        $cashBillId = BillType::CASH->value;      // ID счета для наличных
        $terminalBillId = BillType::CARD->value;  // ID счета для безнала

        $output->writeln('<info>Shifts begin migration...</info>');

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
                        'comment' => 'Выручка наличными #'.$shift->id.' за '.$transactionDate->format('d/m/Y'),
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
                        'comment' => 'Выручка по терминалу #'.$shift->id.' за '.$transactionDate->format('d/m/Y'),
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
        $output->writeln('<info>Shifts end migration...</info>');
    }

    public function down(): void
    {
        // Удаляем только те приходы, которые были импортированы из смен
        DB::table('transactions')
            ->where('comment', 'like', 'Выручка % (Импорт из смены #%')
            ->delete();
    }
};
