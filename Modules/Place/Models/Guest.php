<?php

namespace Modules\Place\Models;

use App\Casts\PhoneCast;
use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'place_id', 'phone', 'comment'];

    protected $casts = [
        'phone' => PhoneCast::class,
    ];

    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getFullNameAttribute()
    {
        if (isset($this->comment)) {
            return $this->name.' ('.$this->comment.')';
        } else {
            return $this->name;
        }
    }
}
