<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price'];

    public $timestamps = false;

    public function Storage(): BelongsToMany
    {
        return $this->belongsToMany(Storage::class);
    }
}
