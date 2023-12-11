<?php

namespace App\Observers;

use App\Models\{Bill, Shift};

class UpdateShiftBill
{
    public function created(Shift $shift)
    {
        Bill::updateOrCreate(
            ['id' => 1],
            ['balance' => $shift->shift]
        );
        Bill::updateOrCreate(
            ['id' => 2]
        )
        ->increment('balance', $shift->terminal*0.987 );
    }

    // public function updated(Shift $shift)
    // {
    //     Bill::updateOrCreate(
    //         ['id' => $model->bill_id]
    //     )
    //     ->increment('balance', $model->getOriginal('sum') );

    //     Bill::updateOrCreate(
    //         ['id' => $model->bill_id]
    //     )
    //     ->decrement('balance', $model->sum );
    // }

    // public function deleted(Shift $shift)
    // {
    //     Bill::updateOrCreate(
    //         ['id' => $model->bill_id]
    //     )
    //     ->increment('balance', $model->sum );
    // }
}
