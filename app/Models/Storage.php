<?php

namespace App\Models;

use App\Enums\Storage as EnumsStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use HasFactory;

    protected $fillable = ['place_id', 'name'];

    protected $casts = [
        'own' => EnumsStorage::class,
    ];

    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function items()
    {
        return $this->HasMany(Item::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items()->sum('count');
    }

    public function getTotalSumAttribute(): float
    {
        return $this->items()->get()->sum(function ($item) {
            return $item->price * $item->count;
        });
    }
}
