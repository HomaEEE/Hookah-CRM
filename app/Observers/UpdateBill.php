<?php

namespace App\Observers;

use App\Models\Bill;
use Illuminate\Database\Eloquent\Model;

class UpdateBill
{
    public function created(Model $model)
    {
        Bill::updateOrCreate(
            ['id' => $model->bill_id]
        )
        ->decrement('balance', $model->sum );
    }

    public function updated(Model $model)
    {
        Bill::updateOrCreate(
            ['id' => $model->bill_id]
        )
        ->increment('balance', $model->getOriginal('sum') );

        Bill::updateOrCreate(
            ['id' => $model->bill_id]
        )
        ->decrement('balance', $model->sum );
    }

    public function deleted(Model $model)
    {
        Bill::updateOrCreate(
            ['id' => $model->bill_id]
        )
        ->increment('balance', $model->sum );
    }
}
