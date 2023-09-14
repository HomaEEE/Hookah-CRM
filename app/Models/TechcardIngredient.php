<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechcardIngredient extends Model
{
    protected $table = 'techcard_ingredient';

    public $timestamps = false;


    public function techcard()
    {
        return $this->hasOne(Techcard::class, 'shift_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }


    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'id');
    }

}
