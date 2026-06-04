<?php

namespace Modules\Place\Models;

use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reqvizite extends Model
{
    use HasFactory;

    protected $fillable = ['room', 'tags'];

    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
