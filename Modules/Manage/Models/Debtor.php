<?php

namespace Modules\Manage\Models;

use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debtor extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id',
        'name',
        'phone',
        'notes',
    ];

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(InventorySale::class);
    }
}
