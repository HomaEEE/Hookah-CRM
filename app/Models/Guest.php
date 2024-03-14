<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'place_id', 'phone', 'comment'];

    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getFullNameAttribute()
    {
        if (isset($this->comment)) {
        return $this->name . ' (' . $this->comment . ')';
        } else {
            return $this->name;
        }
    }

}
