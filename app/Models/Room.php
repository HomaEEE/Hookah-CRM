<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory, Compoships;

    protected $fillable = ['room', 'tags'];

    public $timestamps = false;

    public function seets()
    {
        return $this->hasMany(Seet::class, 'room_id');
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class, 'place_id');
    }

    public function getRoomNameAttribute()
    {
        return $this->room;
    }
}
