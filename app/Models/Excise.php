<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Excise extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = ['sum', 'added_at', 'title', 'excise', 'volume', 'user_id', 'place_id'];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
}
