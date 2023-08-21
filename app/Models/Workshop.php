<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = ['place_id', 'name', 'procent'];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function wage()
    {
        return $this->belongsTo(Wage::class, 'place_id');
    }

    public function writeoff()
    {
        return $this->hasMany(Writeoff::class, 'workshop_id');
    }
}
