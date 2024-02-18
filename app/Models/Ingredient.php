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

    public function getNameAttribute()
    {
        return $this->ingredient;
    }

}
