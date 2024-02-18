<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'place_id', 'table_id', 'date', 'guest_id', 'time', 'person', 'comment'];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function seet()
    {
        return $this->belongsTo(Seet::class, 'table_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }
}
