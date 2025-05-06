<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leftover extends Model
{
    use HasFactory;

    protected $fillable = ['place_id', 'supply_id', 'ingredient_id', 'workshop_id', 'qty'];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
