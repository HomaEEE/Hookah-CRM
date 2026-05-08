<?php

namespace Modules\Manage\Models;

use App\Casts\MoneyCast;
use App\Enums\IngredientUnit;
use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Menu\Models\Techcard;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Ingredient extends Model
{
    use HasFactory;
    use HasJsonRelationships;

    protected $fillable = ['ingredient', 'unit', 'workshop_id', 'place_id', 'price'];

    protected $casts = [
        'unit' => IngredientUnit::class,
        'price' => MoneyCast::class,
    ];

    // protected $casts = [
    //     'unit' => SupplyUnit::class,
    // ];
    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function techcard()
    {
        return $this->belongsTo(Techcard::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function whiteoff()
    {
        return $this->belongsTo(Writeoff::class);
    }

    public function getNameAttribute()
    {
        return $this->ingredient;
    }
}
