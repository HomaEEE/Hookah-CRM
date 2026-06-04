<?php

namespace Modules\Manage\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

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
