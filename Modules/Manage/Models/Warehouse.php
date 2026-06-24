<?php

namespace Modules\Manage\Models;

use App\Models\Place;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = ['name', 'description'];

    public function items()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }
}
