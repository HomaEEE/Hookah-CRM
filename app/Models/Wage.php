<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wage extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'place_id', 'wage', 'procent'];

    public $timestamps = false;

    protected $casts = ['procent' => 'array'];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function workshops()
    {
        return $this->belongsTo(Workshop::class, 'workshop_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
}
