<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Finance\Enums\BillType;
use Modules\Finance\Enums\TransactionType;
use Symfony\Component\Console\Output\ConsoleOutput;

return new class extends Migration
{
    public function up(): void
    {
        $output = new ConsoleOutput; //

        // 1. Перенос Расходов (Expenses) -> TransactionType::Expense
        $output->writeln('<info>Starting Expenses migration...</info>'); //

        if (Schema::hasTable('expenses')) {

            DB::table('expenses')->orderBy('id')->chunk(500, function ($expenses) {
                $transactions = [];
                foreach ($expenses as $expense) {

                    $newBillId = match ((string) $expense->bill_id) {
                        '0' => 1, // Или лучше BillType::CASH->value
                        '1' => 2, // Или лучше BillType::CARD->value
                        default => null, // Если был null или мусор - оставляем null
                    };

                    $transactions[] = [
                        'type' => TransactionType::Expense->value, // '1'
                        'sum' => $expense->sum ?? '0',
                        'user_id' => $expense->user_id,
                        'place_id' => $expense->place_id,
                        'expense_category_id' => $expense->category_id ?? null,
                        'supply_id' => $expense->supply_id ?? null,
                        'bill_id' => $newBillId, // Используем безопасное значение
                        'poster' => $expense->poster ?? null,
                        'comment' => $expense->comment ?? null,
                        'added_at' => $expense->added_at ?? $expense->created_at ?? now(),
                        'created_at' => $expense->created_at ?? now(),
                        'updated_at' => $expense->updated_at ?? now(),
                    ];
                }
                DB::table('transactions')->insert($transactions);
            });

            $output->writeln('<info>Finish Expenses migration...</info>'); //

        }

        // 2. Перенос Приходов (Incomes) -> TransactionType::Income
        $output->writeln('<info>Starting Incomes migration...</info>'); //

        if (Schema::hasTable('incomes')) {

            DB::table('incomes')->orderBy('id')->chunk(500, function ($incomes) {
                $transactions = [];
                foreach ($incomes as $income) {
                    $transactions[] = [
                        'type' => TransactionType::Income->value, // '2'
                        'sum' => $income->sum,
                        'user_id' => $income->user_id,
                        'place_id' => $income->place_id,
                        'bill_id' => BillType::CASH->value,
                        'added_at' => $income->added_at ?? $income->created_at ?? now(), // У incomes нет поля added_at
                        'created_at' => $income->created_at ?? now(),
                        'updated_at' => $income->updated_at ?? now(),
                    ];
                }
                DB::table('transactions')->insert($transactions);
            });
            $output->writeln('<info>Finish Incomes migration...</info>'); //
        }

        // 3. Перенос Инкассаций (Withdrawals) -> TransactionType::Withdrawal
        $output->writeln('<info>Starting withdrawals migration...</info>'); //

        if (Schema::hasTable('withdrawals')) {

            DB::table('withdrawals')->orderBy('id')->chunk(500, function ($withdrawals) {
                $transactions = [];
                foreach ($withdrawals as $withdrawal) {
                    $transactions[] = [
                        'type' => TransactionType::Withdrawal->value, // '4'
                        'sum' => $withdrawal->sum,
                        'bill_id' => $withdrawal->bill_id + 1,
                        'user_id' => $withdrawal->user_id,
                        'place_id' => $withdrawal->place_id,
                        'added_at' => $withdrawal->added_at ?? $withdrawal->created_at ?? now(),
                        'created_at' => $withdrawal->created_at ?? now(),
                        'updated_at' => $withdrawal->updated_at ?? now(),
                    ];
                }
                DB::table('transactions')->insert($transactions);
            });
        }

        $output->writeln('<comment>Transactions table successfully updated.</comment>');
    }

    public function down(): void
    {
        DB::table('transactions')->truncate();
    }
};
