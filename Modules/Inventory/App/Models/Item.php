<?php

namespace Modules\Inventory\App\Models;

use App\Casts\MoneyCast;
use App\Models\Place;
use App\Models\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class Item extends Model
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['name', 'price', 'qty', 'serial_number', 'storage_id'];

    protected $casts = ['price' => MoneyCast::class];

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
