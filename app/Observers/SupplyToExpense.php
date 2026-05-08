<?php

namespace App\Observers;

use App\Casts\MoneyCast;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Modules\Finance\Models\Expense;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Manage\Models\Ingredient;
use Modules\Manage\Models\Supply;

class SupplyToExpense
{
    public function created(Supply $supply): void
    {
        if ($supply->pay == true) {
            $this->recordIngredientHistory($supply);

            Expense::create(
                [
                    'user_id' => Auth::user()->id,
                    'place_id' => $supply->place_id,
                    'supply_id' => $supply != null ? $supply->id : 0,
                    'category_id' => ExpenseCategory::where(['place_id' => Filament::getTenant()->id, 'name' => 'Поставки'])->value('id'),
                    'sum' => $supply->sum,
                    'bill_id' => $supply->bill_id,
                    'comment' => $supply->comment,
                ]
            );
        }
    }

    public function updated(Supply $supply): void
    {
        if ($supply->pay == true) {
            $this->recordIngredientHistory($supply);

            Expense::updateOrCreate(
                [
                    'place_id' => $supply->place_id,
                    'supply_id' => $supply->id,
                ],
                [
                    'sum' => $supply->sum,
                    'user_id' => Auth::user()->id,
                    'category_id' => ExpenseCategory::where(['place_id' => Filament::getTenant()->id, 'name' => 'Поставки'])->value('id'),
                    'bill_id' => $supply->bill_id,
                    'comment' => $supply->comment,
                ]
            );
        } elseif ($supply->pay == false) {
            Expense::where([
                'supply_id' => $supply->id,
                'sum' => $supply->sum,
            ])->delete();
        }
    }

    public function deleted(Supply $supply): void
    {
        Expense::where('supply_id', $supply->id)->delete();
    }

    protected function recordIngredientHistory(Supply $supply): void
    {
        $items = $supply->items;

        if (! is_array($items)) {
            return;
        }

        // foreach ($items as $item) {
        //     $ingredientId = $item['ingredient_id'] ?? null;
        //     $newPrice = $item['price'] ?? null;

        //     if (! $ingredientId || $newPrice === null) {
        //         continue;
        //     }

        //     $ingredient = Ingredient::find($ingredientId);

        //     if (! $ingredient) {
        //         continue;
        //     }

        //     $moneyCast = new MoneyCast;
        //     $newPrice = (int) $moneyCast->set($ingredient, 'price', $newPrice, []);

        //     $currentPrice = (int) $ingredient->getRawOriginal('price');

        //     if ($newPrice !== $currentPrice) {
        //         $ingredient->update(['price' => $newPrice]);

        //         IngredientHistory::create([
        //             'ingredient_id' => $ingredientId,
        //             'price' => $newPrice,
        //         ]);
        //     }
        // }
    }
}
