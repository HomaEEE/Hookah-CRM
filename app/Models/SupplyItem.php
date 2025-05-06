<?php

namespace App\Models;

use Awobaz\Compoships\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SupplyItem extends Pivot
{
    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
