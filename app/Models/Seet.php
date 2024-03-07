<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seet extends Model
{
    use HasFactory, Compoships;

    protected $fillable = ['table_id', 'tags', 'room_id', 'place_id'];

    public $timestamps = false;

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function booking()
    {
        return $this->belongsToMany(Booking::class, 'id', 'table');
    }
}
