<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Storage extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['place_id', 'workshop_id', 'item_id', 'qty'];

    public $timestamps = false;

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function items()
    {
        return $this->belongsToMany(Item::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

}
