<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Item extends Model implements HasMedia
{
    use HasFactory,  InteractsWithMedia;

    protected $fillable = ['name', 'price'];

    protected $casts = ['sum' => MoneyCast::class];

    public $timestamps = false;

    public function Storage(): BelongsToMany
    {
        return $this->belongsToMany(Storage::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
