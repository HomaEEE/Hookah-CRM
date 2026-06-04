<?php

namespace Modules\Place\Models;

use App\Casts\PhoneCast;
use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company', 'place_id', 'phone', 'comment'];

    protected $casts = [
        'phone' => PhoneCast::class,
    ];

    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
}
