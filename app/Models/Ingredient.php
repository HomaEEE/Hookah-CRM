<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['ingredient', 'unit', 'workshop_id', 'place_id', 'price'];

    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function places()
    {
        return $this->BelongsToMany(Place::class, 'place_supplier', 'supplier_id', 'place_id');
    }

    public function techcard()
    {
        return $this->belongsTo(Techcard::class,  'techcard_ingredient', 'techcard_id', 'ingredient_id');
    }

    public function techcards()
    {
        return $this->belongsToMany(Techcard::class);
    }


}
