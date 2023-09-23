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

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'place_id');
    }

}
