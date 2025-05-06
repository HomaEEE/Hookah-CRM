<?php

namespace App\Models;

use App\Enums\MeterType;
use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meter extends Model
{
    use HasFactory, Compoships;

    protected $fillable = ['place_id', 'room_id', 'type', 'price', 'data'];

    protected $casts = [
        'type' => MeterType::class
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function getMeterNameAttribute()
    {
        return $this->room;
    }

    public function indications()
    {
        return $this->HasMany(Indication::class);
    }
}
