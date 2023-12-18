<?php

namespace App\Models;

use App\Enums\Storage as EnumsStorage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use HasFactory;

    protected $fillable = ['place_id', 'workshop_id', 'item_id', 'qty'];

    protected $casts = [
        'own' => EnumsStorage::class,
    ];

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
