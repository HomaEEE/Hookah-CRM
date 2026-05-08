<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Modules\Manage\Models\Ingredient;
use Modules\Manage\Models\Supply;

class SupplyItem extends Pivot
{
    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function item()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
