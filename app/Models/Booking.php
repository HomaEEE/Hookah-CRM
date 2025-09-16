<?php

namespace App\Models;

use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    use HasJsonRelationships;

    protected $fillable = ['user_id', 'place_id', 'seet_ids', 'date', 'guest_id', 'time', 'person', 'comment'];

    protected $casts = ['seet_ids' => 'json'];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function seet()
    {
        return $this->belongsToJson(Seet::class, 'seet_ids');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function getBookingTimeAttribute()
    {
        return (new Carbon($this->time))->translatedFormat('l d/m') .', '
        . (new Carbon($this->time))->translatedFormat('H:i');
    }
}
