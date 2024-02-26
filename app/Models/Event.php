<?php

namespace App\Models;

use App\Enums\EventType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'type', 'datetime', 'end_time', 'comment', 'place_id'];

    protected $casts = [
        'type' => EventType::class
    ];

    protected $dates = ['datetime', 'end_time'];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function getEventTimeAttribute()
    {
        return (new Carbon($this->datetime))->translatedFormat('l d/m') .', '
        . (new Carbon($this->datetime))->translatedFormat('H:i') . '-'
        . (new Carbon($this->end_time))->translatedFormat('H:i');
    }

}