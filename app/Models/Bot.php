<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    protected $table = 'telegraph_bots';

    use HasFactory;

    protected $fillable = ['token', 'name', 'place_id'];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
}
