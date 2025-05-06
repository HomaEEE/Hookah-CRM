<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tip extends Model
{
    use HasFactory;

    protected $fillable = ['place_id', 'tips'];

    protected $casts = [
        'tips' => MoneyCast::class,
    ];


    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
}
