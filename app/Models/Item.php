<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Item extends Model implements HasMedia
{
    use HasFactory,  InteractsWithMedia;

    protected $fillable = ['name', 'price'];

    public $timestamps = false;

    public function Storage(): BelongsToMany
    {
        return $this->belongsToMany(Storage::class);
    }
}
