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
    use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

    protected $fillable = ['product_name', 'workshop_id', 'process', 'ingredients'];

    protected $casts = ['ingredients' => 'json'];

    public $timestamps = false;


    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ingredient(): HasOne
    {
        return $this->hasOne(Ingredient::class, 'ingredients->ingredient_id');
    }
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class, 'id');
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
}

