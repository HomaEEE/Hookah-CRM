<?php

namespace App\Observers;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Modules\Finance\Enums\TransactionType;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Finance\Models\Transaction;
use Modules\Manage\Models\Supply;

class SupplyObserver
{
    public function created(Supply $supply): void
    {
        if ($supply->pay) {
            $this->recordIngredientHistory($supply);
            $this->syncTransaction($supply);
        }
    }

    public function updated(Supply $supply): void
    {
        if ($supply->pay) {
            //  $this->recordIngredientHistory($supply);
            $this->syncTransaction($supply);
        } else {
            // Если галочка оплаты снята — удаляем транзакцию (ищем ТОЛЬКО по supply_id)
            Transaction::where('supply_id', $supply->id)->delete();
        }
    }

    public function deleted(Supply $supply): void
    {
        Transaction::where('supply_id', $supply->id)->delete();
    }

    /**
     * Единый метод для создания или обновления транзакции
     */
    protected function syncTransaction(Supply $supply): void
    {
        Transaction::updateOrCreate(
            [
                'supply_id' => $supply->id,
            ],
            [
                // Указываем тип из Enum, который мы создали ранее
                'type' => TransactionType::Expense->value,

                // Auth::id() вернет null, если пользователя нет (безопасно)
                'user_id' => Auth::id() ?? $supply->user_id,
                'place_id' => $supply->place_id,
                'expense_category_id' => $this->getSupplyCategoryId(),
                'sum' => $supply->sum,
                'bill_id' => $supply->bill_id,
                'comment' => $supply->comment,
                'added_at' => $supply->created_at ?? now(),
            ]
        );
    }

    protected function getSupplyCategoryId(): ?int
    {
        $tenantId = Filament::getTenant()?->id;

        if (! $tenantId) {
            return null;
        }

        return ExpenseCategory::where('place_id', $tenantId)
            ->where('name', 'Поставки')
            ->value('id');
    }

    protected function recordIngredientHistory(Supply $supply): void {}
}
