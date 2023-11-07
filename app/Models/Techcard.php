<?php

namespace App\Models;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Techcard extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['product_name', 'workshop', 'process', 'ingredients'];

    protected $casts = ['ingredients' => 'array'];

    protected $attributes = ['ingredients' => '[]'];

    public $timestamps = false;


    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
}

