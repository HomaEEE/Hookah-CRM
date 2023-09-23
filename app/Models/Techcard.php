<?php

namespace App\Models;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Techcard extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['product_name', 'workshop', 'process', 'ingredients'];

    protected $casts = ['ingredients' => 'array'];

    public $timestamps = false;


    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class);
    }

    public function ingredientTechcards(): HasMany
{
    return $this->hasMany(IngredientTechcard::class);
}


    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
}
